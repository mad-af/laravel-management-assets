<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case PREVENTIVE = 'preventive';
    case CORRECTIVE = 'corrective';

    public function label(): string
    {
        return match($this) {
            self::PREVENTIVE => 'Preventive',
            self::CORRECTIVE => 'Corrective',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PREVENTIVE => 'Scheduled maintenance to prevent issues',
            self::CORRECTIVE => 'Maintenance to fix existing problems',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::PREVENTIVE => 'badge-info',
            self::CORRECTIVE => 'badge-warning',
        };
    }
}