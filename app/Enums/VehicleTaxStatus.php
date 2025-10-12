<?php

namespace App\Enums;

enum VehicleTaxStatus: string
{
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case DUE_SOON = 'due_soon';
    case UPCOMING = 'upcoming';

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'Dibayar',
            self::OVERDUE => 'Terlambat',
            self::DUE_SOON => 'Jatuh Tempo',
            self::UPCOMING => 'Akan Datang',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PAID => 'success',
            self::OVERDUE => 'error',
            self::DUE_SOON => 'warning',
            self::UPCOMING => 'info',
        };
    }
}