<?php

namespace App\Enums;

enum AssetTransferType: string
{
    case BRANCH = 'branch';
    case COMPANY = 'company';

    public function label(): string
    {
        return match ($this) {
            self::BRANCH => 'Branch',
            self::COMPANY => 'Company',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BRANCH => 'Transfer aset antar cabang',
            self::COMPANY => 'Transfer aset antar perusahaan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BRANCH => 'secondary',
            self::COMPANY => 'primary',
        };
    }

}