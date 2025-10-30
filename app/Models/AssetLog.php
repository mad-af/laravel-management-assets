<?php

namespace App\Models;

use App\Enums\AssetLogAction;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'user_id',
        'action',
        'changed_fields',
        'notes',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'action' => AssetLogAction::class,
    ];

    /**
     * Get the asset that owns the log.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hook into model events to enrich notes based on status changes.
     */
    protected static function booted()
    {
        static::created(function (self $log) {
            $fields = $log->changed_fields ?? [];
            if (! is_array($fields)) {
                return;
            }

            if (! isset($fields['status'])) {
                return;
            }

            $old = $fields['status']['old'] ?? null;
            $new = $fields['status']['new'] ?? null;

            $oldVal = self::normalizeStatus($old);
            $newVal = self::normalizeStatus($new);

            if (! $oldVal || ! $newVal) {
                return;
            }

            $message = null;
            $newAction = null;
            // Maintenance start/end
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::MAINTENANCE->value) {
                $message = 'Aset ini kini masuk masa perawatan';
                $newAction = AssetLogAction::MAINTENANCE_START;
            } elseif ($oldVal === AssetStatus::MAINTENANCE->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset selesai perawatan dan siap dipakai kembali';
                $newAction = AssetLogAction::MAINTENANCE_END;
            }

            // Loan start/end
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::ON_LOAN->value) {
                $message = 'Aset sedang dipinjamkan';
                $newAction = AssetLogAction::CHECKED_OUT;
            } elseif ($oldVal === AssetStatus::ON_LOAN->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset dikembalikan dan tersedia kembali';
                $newAction = AssetLogAction::CHECKED_IN;
            }

            // Transfer start/end (gunakan STATUS_CHANGED karena tidak ada enum khusus transfer)
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::IN_TRANSFER->value) {
                $message = 'Aset sedang dalam proses pemindahan';
                $newAction = AssetLogAction::STATUS_CHANGED;
            } elseif ($oldVal === AssetStatus::IN_TRANSFER->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset telah diterima dan aktif kembali';
                $newAction = AssetLogAction::STATUS_CHANGED;
            }

            if ($message || $newAction) {
                if ($message) {
                    $log->notes = $message;
                }
                if ($newAction) {
                    $log->action = $newAction;
                }
                $log->save();
            }
        });
    }

    /**
     * Normalize status value to string for comparison.
     */
    private static function normalizeStatus($value): ?string
    {
        if ($value instanceof AssetStatus) {
            return $value->value;
        }
        if (is_string($value)) {
            return $value;
        }

        return null;
    }

    /**
     * Get localized label for status string.
     */
    private static function labelFor(string $value): string
    {
        return AssetStatus::from($value)->label();
    }
}
