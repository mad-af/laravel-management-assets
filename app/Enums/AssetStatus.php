<?php

namespace App\Enums;

enum AssetStatus: string
{
    case ACTIVE = 'active';
    case DAMAGED = 'damaged';
    case LOST = 'lost';
    case MAINTENANCE = 'maintenance';
    case CHECKED_OUT = 'checked_out';

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
            self::ACTIVE => 'Active',
            self::DAMAGED => 'Damaged',
            self::LOST => 'Lost',
            self::MAINTENANCE => 'Under Maintenance',
            self::CHECKED_OUT => 'Checked Out',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::DAMAGED => 'red',
            self::LOST => 'gray',
            self::MAINTENANCE => 'yellow',
            self::CHECKED_OUT => 'blue',
        };
    }

    /**
     * Get badge color class for UI display
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::ACTIVE => 'badge-success',
            self::DAMAGED => 'badge-error',
            self::LOST => 'badge-neutral',
            self::MAINTENANCE => 'badge-warning',
            self::CHECKED_OUT => 'badge-info',
        };
    }

    /**
     * Check if asset is available for checkout
     */
    public function isAvailable(): bool
    {
        return $this === self::ACTIVE;
    }
}