<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'action',
        'changed_fields',
        'notes',
    ];

    protected $casts = [
        'changed_fields' => 'array',
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
     * Scope a query to only include logs for a specific asset.
     */
    public function scopeForAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope a query to only include logs with a specific action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the action badge color.
     */
    public function getActionBadgeColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'badge-success',
            'updated' => 'badge-info',
            'deleted' => 'badge-error',
            'status_changed' => 'badge-warning',
            default => 'badge-ghost',
        };
    }

    /**
     * Get formatted changed fields for display.
     */
    public function getFormattedChangesAttribute(): string
    {
        if (!$this->changed_fields) {
            return 'No changes recorded';
        }

        $changes = [];
        foreach ($this->changed_fields as $field => $change) {
            $oldValue = $change['old'] ?? 'N/A';
            $newValue = $change['new'] ?? 'N/A';
            $changes[] = ucfirst($field) . ": {$oldValue} → {$newValue}";
        }

        return implode(', ', $changes);
    }
}
