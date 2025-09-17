<?php

namespace App\Enums;

enum AssetLocationChangeType: string
{
    case TRANSFER = 'transfer';
    case MAINTENANCE = 'maintenance';
    case LOAN = 'loan';
    case RETURN = 'return';
    case DISPOSAL = 'disposal';
    case INITIAL = 'initial';
    case CORRECTION = 'correction';
    case AUDIT = 'audit';

    public function label(): string
    {
        return match ($this) {
            self::TRANSFER => 'Transfer',
            self::MAINTENANCE => 'Maintenance',
            self::LOAN => 'Loan',
            self::RETURN => 'Return',
            self::DISPOSAL => 'Disposal',
            self::INITIAL => 'Initial Assignment',
            self::CORRECTION => 'Correction',
            self::AUDIT => 'Audit Adjustment',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TRANSFER => 'Pemindahan aset antar lokasi',
            self::MAINTENANCE => 'Pemindahan untuk keperluan maintenance',
            self::LOAN => 'Peminjaman aset',
            self::RETURN => 'Pengembalian aset',
            self::DISPOSAL => 'Pemindahan untuk disposal',
            self::INITIAL => 'Penempatan awal aset',
            self::CORRECTION => 'Koreksi lokasi aset',
            self::AUDIT => 'Penyesuaian hasil audit',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRANSFER => 'info',
            self::MAINTENANCE => 'warning',
            self::LOAN => 'primary',
            self::RETURN => 'success',
            self::DISPOSAL => 'error',
            self::INITIAL => 'secondary',
            self::CORRECTION => 'warning',
            self::AUDIT => 'info',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}