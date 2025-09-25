<?php

namespace App\Enums;

enum AssetTransferStatus: string
{
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SHIPPED => 'info',
            self::DELIVERED => 'success',
        };
    }

}