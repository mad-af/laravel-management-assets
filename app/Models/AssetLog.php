<?php

namespace App\Models;

use App\Enums\AssetLogAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
     * Scope a query to only include logs for a specific asset.
     */
    public function scopeForAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope a query to only include logs with a specific action.
     */
    public function scopeByAction($query, AssetLogAction|string $action)
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
            AssetLogAction::CREATED->value => 'badge-success',
        AssetLogAction::UPDATED->value => 'badge-info',
        AssetLogAction::DELETED->value => 'badge-error',
        AssetLogAction::STATUS_CHANGED->value => 'badge-warning',
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

        // Handle both string JSON and array formats
        $changedFields = is_string($this->changed_fields) 
            ? json_decode($this->changed_fields, true) 
            : $this->changed_fields;

        if (!is_array($changedFields)) {
            return 'Invalid change data format';
        }

        $changes = [];
        foreach ($changedFields as $field => $change) {
            $oldValue = $change['old'] ?? 'N/A';
            $newValue = $change['new'] ?? 'N/A';
            $changes[] = ucfirst($field) . ": {$oldValue} → {$newValue}";
        }

        return implode(', ', $changes);
    }

    /**
     * Check if log action is critical
     */
    public function isCritical(): bool
    {
        return $this->action?->isCritical() ?? false;
    }

    /**
     * Check if log action is positive
     */
    public function isPositive(): bool
    {
        return $this->action?->isPositive() ?? false;
    }

    /**
     * Get action icon
     */
    public function getActionIcon(): string
    {
        return $this->action?->icon() ?? 'circle';
    }

    /**
     * Get action color
     */
    public function getActionColor(): string
    {
        return $this->action?->color() ?? 'gray';
    }

    /**
     * Scope to get critical logs
     */
    public function scopeCritical($query)
    {
        return $query->whereIn('action', [
            AssetLogAction::DELETED,
            AssetLogAction::DAMAGED,
            AssetLogAction::LOST,
        ]);
    }

    /**
     * Scope to get condition-related logs
     */
    public function scopeConditionLogs($query)
    {
        return $query->whereIn('action', AssetLogAction::conditionActions());
    }

    /**
     * Scope to get location-related logs
     */
    public function scopeLocationLogs($query)
    {
        return $query->whereIn('action', AssetLogAction::locationActions());
    }

    /**
     * Scope to get recent logs (last 30 days)
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
