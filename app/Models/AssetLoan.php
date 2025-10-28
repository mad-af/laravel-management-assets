<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'checkout_at',
        'due_at',
        'checkin_at',
        'condition_out',
        'condition_in',
        'notes',
    ];

    protected $casts = [
        'checkout_at' => 'datetime',
        'due_at' => 'datetime',
        'checkin_at' => 'datetime',
        'condition_out' => AssetCondition::class,
        'condition_in' => AssetCondition::class,
    ];

    /**
     * Get the asset that is being loaned.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the employee who borrowed the asset.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Helper: whether the loan is currently active (not returned).
     */
    public function isActive(): bool
    {
        return $this->checkin_at === null;
    }

    /**
     * Helper: whether the active loan is overdue based on due_at.
     */
    public function isOverdue(): bool
    {
        return $this->checkin_at === null && $this->due_at !== null && $this->due_at->isPast();
    }

    /**
     * Scope: only active loans (not returned).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('checkin_at');
    }

    /**
     * Scope: active loans that are overdue.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('checkin_at')
            ->where('due_at', '<', now());
    }

    /**
     * Auto-fill condition_out from the asset's current condition when creating.
     * Also mark the asset as on_loan once the loan is created.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (AssetLoan $loan) {
            // If not provided, default condition_out to the asset's condition
            if (empty($loan->condition_out) && ! empty($loan->asset_id)) {
                $asset = Asset::find($loan->asset_id);
                if ($asset && $asset->condition) {
                    $loan->condition_out = $asset->condition;
                }
            }
        });

        static::created(function (AssetLoan $loan) {
            // Update asset status to ON_LOAN when loan is created and not yet returned
            if (! empty($loan->asset_id) && $loan->checkin_at === null) {
                $asset = $loan->asset;
                if ($asset) {
                    $asset->status = AssetStatus::ON_LOAN;
                    $asset->save();
                }
            }
        });

        static::updated(function (AssetLoan $loan) {
            // Update asset status to AVAILABLE when loan is returned
            if (! empty($loan->asset_id)) {
                $asset = $loan->asset;
                if ($asset) {
                    if ($loan->checkin_at !== null) {
                        $asset->status = AssetStatus::ACTIVE;
                    }

                    if (! empty($loan->condition_in)) {
                        $asset->condition = $loan->condition_in;
                    }

                    $asset->save();
                }
            }
        });
    }
}
