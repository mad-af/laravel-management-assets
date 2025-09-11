<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case OPEN = 'open';
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::OPEN => 'badge-info',
            self::SCHEDULED => 'badge-warning',
            self::IN_PROGRESS => 'badge-primary',
            self::COMPLETED => 'badge-success',
            self::CANCELLED => 'badge-error',
        };
    }
}