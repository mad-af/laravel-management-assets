<?php

namespace App\Enums;

enum MaintenancePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::LOW => 'badge-info',
            self::MEDIUM => 'badge-warning',
            self::HIGH => 'badge-error',
            self::CRITICAL => 'badge-error',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::CRITICAL => 1,
            self::HIGH => 2,
            self::MEDIUM => 3,
            self::LOW => 4,
        };
    }
}