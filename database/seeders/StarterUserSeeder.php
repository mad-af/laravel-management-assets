<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StarterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = config('starter.admin.name');
        $email = config('starter.admin.email');
        $password = config('starter.admin.password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => UserRole::ADMIN,
                'password' => Hash::make($password),
                'email_verified_at' => null,
            ]
        );
    }
}
