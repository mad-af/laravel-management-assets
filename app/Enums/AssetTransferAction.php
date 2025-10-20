<?php

namespace App\Enums;

enum AssetTransferAction: string
{
    case DELIVERY = 'delivery';
    case CONFIRMATION = 'confirmation';

    public function label(): string
    {
        return match ($this) {
            self::DELIVERY => 'Pengiriman',
            self::CONFIRMATION => 'Konfirmasi',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::DELIVERY => 'info',
            self::CONFIRMATION => 'success',
        };
    }

}