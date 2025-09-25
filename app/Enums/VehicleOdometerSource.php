<?php

namespace App\Enums;

enum VehicleOdometerSource: string
{
    case MANUAL = 'manual';
    case SERVICE = 'service';

    /**
     * Get the display label for the enum value
     */
    public function label(): string
    {
        return match($this) {
            self::MANUAL => 'Manual',
            self::SERVICE => 'Service',
        };
    }

    /**
     * Get the color class for the enum value (centered styling)
     */
    public function color(): string
    {
        return match($this) {
            self::MANUAL => 'info',
            self::SERVICE => 'warning',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->label()];
        })->toArray();
    }
}