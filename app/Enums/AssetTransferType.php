<?php

namespace App\Enums;

enum AssetTransferType: string
{
    case LOCATION = 'location';
    case DEPARTMENT = 'department';
    case COMPANY = 'company';
    case MAINTENANCE = 'maintenance';
    case DISPOSAL = 'disposal';
    case RETURN = 'return';

    public function label(): string
    {
        return match ($this) {
            self::LOCATION => 'Location Transfer',
            self::DEPARTMENT => 'Department Transfer',
            self::COMPANY => 'Company Transfer',
            self::MAINTENANCE => 'Maintenance Transfer',
            self::DISPOSAL => 'Disposal Transfer',
            self::RETURN => 'Return Transfer',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::LOCATION => 'Transfer aset antar lokasi dalam perusahaan yang sama',
            self::DEPARTMENT => 'Transfer aset antar departemen',
            self::COMPANY => 'Transfer aset antar perusahaan',
            self::MAINTENANCE => 'Transfer aset untuk keperluan maintenance',
            self::DISPOSAL => 'Transfer aset untuk disposal/penghapusan',
            self::RETURN => 'Pengembalian aset ke lokasi asal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOCATION => 'info',
            self::DEPARTMENT => 'primary',
            self::COMPANY => 'warning',
            self::MAINTENANCE => 'secondary',
            self::DISPOSAL => 'error',
            self::RETURN => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}