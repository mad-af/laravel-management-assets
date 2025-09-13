<?php

namespace App\Enums;

enum AssetCondition: string
{
    case EXCELLENT = 'excellent';
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
            self::EXCELLENT => 'Excellent',
            self::GOOD => 'Good',
            self::FAIR => 'Fair',
            self::POOR => 'Poor',
        };
    }

    /**
     * Get enum color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::EXCELLENT => 'green',
            self::GOOD => 'blue',
            self::FAIR => 'yellow',
            self::POOR => 'red',
        };
    }

    /**
     * Get badge color class for UI display
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::EXCELLENT => 'badge-success',
            self::GOOD => 'badge-info',
            self::FAIR => 'badge-warning',
            self::POOR => 'badge-error',
        };
    }

    /**
     * Get condition score (1-4, higher is better)
     */
    public function score(): int
    {
        return match($this) {
            self::EXCELLENT => 4,
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