<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in proper order to maintain referential integrity
        // Production
        $seeder = [StarterUserSeeder::class, CategorySeeder::class];
        // Development
        if (! app()->environment('production')) {
            $seeder = [
                CompanySeeder::class,
                CategorySeeder::class,
                UserSeeder::class,
                BranchSeeder::class,
                UserCompanySeeder::class,
                EmployeeSeeder::class,
                AssetSeeder::class,
                AssetTransferSeeder::class,
                AssetLoanSeeder::class,
                AssetLogSeeder::class,
                VehicleProfileSeeder::class,
            ];
        }
        $this->call($seeder);
    }
}
