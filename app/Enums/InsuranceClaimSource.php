<?php

namespace App\Enums;

enum InsuranceClaimSource: string
{
    case MANUAL = 'manual';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::MAINTENANCE => 'Maintenance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MANUAL => 'info',
            self::MAINTENANCE => 'warning',
        };
    }
}