<?php

namespace App\Enums;

enum AssetLogAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case CHECKED_OUT = 'checked_out';
    case CHECKED_IN = 'checked_in';
    case MAINTENANCE_START = 'maintenance_start';
    case MAINTENANCE_END = 'maintenance_end';
    case CONDITION_CHANGED = 'condition_changed';
    case STATUS_CHANGED = 'status_changed';
    case LOCATION_CHANGED = 'location_changed';
    case CATEGORY_CHANGED = 'category_changed';
    case DAMAGED = 'damaged';
    case LOST = 'lost';
    case FOUND = 'found';
    case REPAIRED = 'repaired';
    case SCANNED = 'scanned';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum label for display
     */
    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Asset Created',
            self::UPDATED => 'Asset Updated',
            self::DELETED => 'Asset Deleted',
            self::CHECKED_OUT => 'Checked Out',
            self::CHECKED_IN => 'Checked In',
            self::MAINTENANCE_START => 'Maintenance Started',
            self::MAINTENANCE_END => 'Maintenance Completed',
            self::CONDITION_CHANGED => 'Condition Changed',
            self::STATUS_CHANGED => 'Status Changed',
            self::LOCATION_CHANGED => 'Location Changed',
            self::CATEGORY_CHANGED => 'Category Changed',
            self::DAMAGED => 'Asset Damaged',
            self::LOST => 'Asset Lost',
            self::FOUND => 'Asset Found',
            self::REPAIRED => 'Asset Repaired',
            self::SCANNED => 'Asset Scanned',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::CREATED => 'green',
            self::UPDATED => 'blue',
            self::DELETED => 'red',
            self::CHECKED_OUT => 'orange',
            self::CHECKED_IN => 'green',
            self::MAINTENANCE_START => 'yellow',
            self::MAINTENANCE_END => 'green',
            self::CONDITION_CHANGED => 'blue',
            self::STATUS_CHANGED => 'blue',
            self::LOCATION_CHANGED => 'purple',
            self::CATEGORY_CHANGED => 'purple',
            self::DAMAGED => 'red',
            self::LOST => 'red',
            self::FOUND => 'green',
            self::REPAIRED => 'green',
            self::SCANNED => 'blue',
        };
    }

    /**
     * Get badge color class for UI display
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::CREATED => 'badge-success',
            self::UPDATED => 'badge-info',
            self::DELETED => 'badge-error',
            self::CHECKED_OUT => 'badge-warning',
            self::CHECKED_IN => 'badge-success',
            self::MAINTENANCE_START => 'badge-warning',
            self::MAINTENANCE_END => 'badge-success',
            self::CONDITION_CHANGED => 'badge-info',
            self::STATUS_CHANGED => 'badge-info',
            self::LOCATION_CHANGED => 'badge-accent',
            self::CATEGORY_CHANGED => 'badge-accent',
            self::DAMAGED => 'badge-error',
            self::LOST => 'badge-error',
            self::FOUND => 'badge-success',
            self::REPAIRED => 'badge-success',
            self::SCANNED => 'badge-info',
        };
    }

    /**
     * Get icon for the action
     */
    public function icon(): string
    {
        return match($this) {
            self::CREATED => 'plus-circle',
            self::UPDATED => 'edit',
            self::DELETED => 'trash',
            self::CHECKED_OUT => 'arrow-right',
            self::CHECKED_IN => 'arrow-left',
            self::MAINTENANCE_START => 'wrench',
            self::MAINTENANCE_END => 'check-circle',
            self::CONDITION_CHANGED => 'activity',
            self::STATUS_CHANGED => 'toggle-left',
            self::LOCATION_CHANGED => 'map-pin',
            self::CATEGORY_CHANGED => 'folder',
            self::DAMAGED => 'alert-triangle',
            self::LOST => 'x-circle',
            self::FOUND => 'search',
            self::REPAIRED => 'tool',
            self::SCANNED => 'scan-line',
        };
    }

    /**
     * Check if action is critical (requires attention)
     */
    public function isCritical(): bool
    {
        return in_array($this, [
            self::DELETED,
            self::DAMAGED,
            self::LOST,
        ]);
    }

    /**
     * Check if action is positive
     */
    public function isPositive(): bool
    {
        return in_array($this, [
            self::CREATED,
            self::CHECKED_IN,
            self::MAINTENANCE_END,
            self::FOUND,
            self::REPAIRED,
        ]);
    }

    /**
     * Get actions related to asset condition
     */
    public static function conditionActions(): array
    {
        return [
            self::CONDITION_CHANGED,
            self::DAMAGED,
            self::REPAIRED,
        ];
    }

    /**
     * Get actions related to asset location
     */
    public static function locationActions(): array
    {
        return [
            self::LOCATION_CHANGED,
            self::CHECKED_OUT,
            self::CHECKED_IN,
            self::LOST,
            self::FOUND,
        ];
    }
}