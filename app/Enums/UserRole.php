<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case AUDITOR = 'auditor';

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
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::STAFF => 'Staff',
            self::AUDITOR => 'Auditor',
        };
    }

    /**
     * Get enum description
     */
    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Akses penuh dan manajemen sistem',
            self::STAFF => 'Pengguna standar dengan akses terbatas',
            self::AUDITOR => 'Akses hanya baca untuk keperluan audit',
        };
    }

    /**
     * Get color for UI display
     */
    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'error',
            self::STAFF => 'info',
            self::AUDITOR => 'warning',
        };

    }
}
