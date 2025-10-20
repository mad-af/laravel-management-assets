<?php

namespace App\Enums;

enum VehicleType: string
{
    case PASSENGER = 'passenger';
    case CARGO = 'cargo';
    case MOTORCYCLE = 'motorcycle';

    public function label(): string
    {
        return match ($this) {
            self::PASSENGER => 'Penumpang',
            self::CARGO => 'Barang',
            self::MOTORCYCLE => 'Sepeda Motor',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PASSENGER => 'info',
            self::CARGO => 'warning',
            self::MOTORCYCLE => 'neutral',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'id' => $case->value,
                'name' => $case->label(),
            ];
        })->toArray();
    }
}