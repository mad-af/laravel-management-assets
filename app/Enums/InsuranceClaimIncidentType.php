<?php

namespace App\Enums;

enum InsuranceClaimIncidentType: string
{
    case COLLISION = 'collision';
    case THEFT = 'theft';
    case FLOOD = 'flood';
    case FIRE = 'fire';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::COLLISION => 'Tabrakan',
            self::THEFT => 'Pencurian',
            self::FLOOD => 'Banjir',
            self::FIRE => 'Kebakaran',
            self::OTHER => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::COLLISION => 'error',
            self::THEFT => 'warning',
            self::FLOOD => 'info',
            self::FIRE => 'error',
            self::OTHER => 'neutral',
        };
    }
}