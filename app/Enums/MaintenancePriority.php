<?php

namespace App\Enums;

enum MaintenancePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';


    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'info',
            self::MEDIUM => 'warning',
            self::HIGH => 'error',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::HIGH => 1,
            self::MEDIUM => 2,
            self::LOW => 3,
        };
    }
}