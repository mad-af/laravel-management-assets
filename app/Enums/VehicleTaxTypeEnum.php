<?php

namespace App\Enums;

enum VehicleTaxTypeEnum: string
{
    case PKB_TAHUNAN = 'pkb_tahunan';
    case KIR = 'kir';

    public function label(): string
    {
        return match ($this) {
            self::PKB_TAHUNAN => 'PKB Tahunan',
            self::KIR => 'KIR',
        };
    }

    public function color(): string
    {
        return match ($this) {
            // Gunakan warna yang konsisten dengan badge di UI
            self::PKB_TAHUNAN => 'info',
            self::KIR => 'warning',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PKB_TAHUNAN => 'Pajak Kendaraan Bermotor Tahunan',
            self::KIR => 'Keur in Orde (Uji Kendaraan Bermotor)',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
            ];
        })->toArray();
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'id' => $case->value,
                'name' => $case->label(),
            ];
        })->toArray();
    }
}