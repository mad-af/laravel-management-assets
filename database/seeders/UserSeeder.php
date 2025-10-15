<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => Str::uuid(),
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'role' => UserRole::ADMIN,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@teknologimaju.co.id',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@teknologimaju.co.id',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@solusidigitak.co.id',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@solusidigitak.co.id',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@inovasibisnis.co.id',
                'role' => UserRole::STAFF,
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
