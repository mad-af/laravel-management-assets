<?php

namespace App\Enums;

enum VehicleInsurancePolicyType: string
{
    case COMPREHENSIVE = 'comprehensive';
    case TLO = 'tlo';
    case TPFT = 'tpft';
    case TPO = 'tpo';

    public function label(): string
    {
        return match ($this) {
            self::COMPREHENSIVE => 'Komprehensif',
            self::TLO => 'Total Loss Only',
            self::TPFT => 'Third Party, Fire & Theft',
            self::TPO => 'Third Party Only',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::COMPREHENSIVE => 'primary',
            self::TLO => 'info',
            self::TPFT => 'warning',
            self::TPO => 'neutral',
        };
    }
}