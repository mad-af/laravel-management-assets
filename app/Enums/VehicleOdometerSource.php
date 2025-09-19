<?php

namespace App\Enums;

enum VehicleOdometerSource: string
{
    case MANUAL = 'manual';
    case TELEMATICS = 'telematics';
    case SERVICE = 'service';

    /**
     * Get the display label for the enum value
     */
    public function label(): string
    {
        return match($this) {
            self::MANUAL => 'Manual',
            self::TELEMATICS => 'Telematics',
            self::SERVICE => 'Service',
        };
    }

    /**
     * Get the color class for the enum value (centered styling)
     */
    public function color(): string
    {
        return match($this) {
            self::MANUAL => 'bg-blue-100 text-blue-800 text-center',
            self::TELEMATICS => 'bg-green-100 text-green-800 text-center',
            self::SERVICE => 'bg-yellow-100 text-yellow-800 text-center',
        };
    }

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum cases with labels
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->label()];
        })->toArray();
    }
}