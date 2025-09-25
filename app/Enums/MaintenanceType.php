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
            self::PREVENTIVE => 'Pemeliharaan terjadwal untuk mencegah masalah',
            self::CORRECTIVE => 'Pemeliharaan untuk memperbaiki masalah yang ada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PREVENTIVE => 'info',
            self::CORRECTIVE => 'warning',
        };
    }
}