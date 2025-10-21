<?php

namespace App\Enums;

enum AssetStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case LOST = 'lost';
    case MAINTENANCE = 'maintenance';
    case ON_LOAN = 'on_loan';
    case IN_TRANSFER = 'in_transfer';

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
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::LOST => 'Hilang',
            self::MAINTENANCE => 'Perawatan',
            self::ON_LOAN => 'Di Pinjamkan',
            self::IN_TRANSFER => 'Di Pindahkan',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'error',
            self::LOST => 'neutral',
            self::MAINTENANCE => 'warning',
            self::ON_LOAN => 'info',
            self::IN_TRANSFER => 'primary',
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