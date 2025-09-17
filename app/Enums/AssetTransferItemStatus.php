<?php

namespace App\Enums;

enum AssetTransferItemStatus: string
{
    case PENDING = 'pending';
    case IN_TRANSIT = 'in_transit';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_TRANSIT => 'In Transit',
            self::DELIVERED => 'Delivered',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_TRANSIT => 'info',
            self::DELIVERED => 'success',
            self::FAILED => 'error',
            self::CANCELLED => 'gray',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu untuk dipindahkan',
            self::IN_TRANSIT => 'Sedang dalam proses pemindahan',
            self::DELIVERED => 'Berhasil dipindahkan ke lokasi tujuan',
            self::FAILED => 'Gagal dipindahkan',
            self::CANCELLED => 'Pemindahan dibatalkan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}