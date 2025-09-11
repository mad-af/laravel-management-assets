<?php

namespace App\Enums;

enum LoanCondition: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';
    case DAMAGED = 'damaged';

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
            self::DAMAGED => 'Damaged',
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
            self::POOR => 'orange',
            self::DAMAGED => 'red',
        };
    }

    /**
     * Check if condition indicates damage or deterioration
     */
    public function isDamaged(): bool
    {
        return $this === self::DAMAGED || $this === self::POOR;
    }

    /**
     * Convert to AssetCondition enum (for updating asset condition)
     */
    public function toAssetCondition(): AssetCondition
    {
        return match($this) {
            self::EXCELLENT => AssetCondition::EXCELLENT,
            self::GOOD => AssetCondition::GOOD,
            self::FAIR => AssetCondition::FAIR,
            self::POOR => AssetCondition::POOR,
            self::DAMAGED => AssetCondition::POOR,
        };
    }
}