<?php

namespace App\Enums;

enum AssetCondition: string
{       
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum label for display
     */
    public function label(): string
    {
        return match($this) {
            self::GOOD => 'Baik',
            self::FAIR => 'Cukup',
            self::POOR => 'Buruk',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::GOOD => 'info',
            self::FAIR => 'warning',
            self::POOR => 'error',
        };
    }

    /**
     * Get condition score (1-4, higher is better)
     */
    public function score(): int
    {
        return match($this) {
            self::GOOD => 3,
            self::FAIR => 2,
            self::POOR => 1,
        };
    }

    /**
     * Check if condition requires attention
     */
    public function needsAttention(): bool
    {
        return $this === self::FAIR || $this === self::POOR;
    }
}