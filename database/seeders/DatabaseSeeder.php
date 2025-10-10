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
        $this->call([
            CompanySeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            BranchSeeder::class,
            UserCompanySeeder::class,
            EmployeeSeeder::class,
            AssetSeeder::class,
            AssetLogSeeder::class,
            VehicleProfileSeeder::class,
            VehicleTaxTypeSeeder::class,
            VehicleTaxHistorySeeder::class,
            AssetMaintenanceSeeder::class,
        ]);
    }
}
