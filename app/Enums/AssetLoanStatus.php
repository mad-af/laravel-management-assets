<?php

namespace App\Enums;

enum AssetLoanStatus: string
{
    case AVAILABLE = 'available';
    case ON_LOAN = 'on_loan';
    case OVERTIME = 'overtime';

    /**
     * Get enum label for display
     */
    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::ON_LOAN => 'Dalam Peminjaman',
            self::OVERTIME => 'Terlambat',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::ON_LOAN => 'info',
            self::OVERTIME => 'error',
        };
    }
}