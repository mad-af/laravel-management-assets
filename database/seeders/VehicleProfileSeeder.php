<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleProfile;
use Illuminate\Database\Seeder;

class VehicleProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vehicle category
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        
        if (!$vehicleCategory) {
            return;
        }

        // Get all vehicle assets
        $vehicleAssets = Asset::where('category_id', $vehicleCategory->id)->get();

        if ($vehicleAssets->count() === 0) {
            return;
        }

        $vehicleProfiles = [
            [
                'asset_id' => $vehicleAssets->first()->id,
                'year_purchase' => 2022,
                'year_manufacture' => 2022,
                'current_odometer_km' => 45000,
                'last_service_date' => '2024-09-15',
                'service_target_odometer_km' => 50000,
                'next_service_date' => '2024-12-15',
                'plate_no' => 'B 1234 ABC',
                'vin' => 'MHFM1CE0XMK123456',
            ],
            [
                'asset_id' => $vehicleAssets->skip(1)->first()?->id,
                'year_purchase' => 2021,
                'year_manufacture' => 2021,
                'current_odometer_km' => 62000,
                'last_service_date' => '2024-08-20',
                'service_target_odometer_km' => 65000,
                'next_service_date' => '2024-11-20',
                'plate_no' => 'D 5678 XYZ',
                'vin' => 'MHFM1CE0XLK789012',
            ],
            [
                'asset_id' => $vehicleAssets->skip(2)->first()?->id,
                'year_purchase' => 2023,
                'year_manufacture' => 2023,
                'current_odometer_km' => 28000,
                'last_service_date' => '2024-10-01',
                'service_target_odometer_km' => 30000,
                'next_service_date' => '2025-01-01',
                'plate_no' => 'L 9012 DEF',
                'vin' => 'MHFM1CE0XNK345678',
            ],
        ];

        foreach ($vehicleProfiles as $profileData) {
            // Only create if asset exists and doesn't already have a profile
            if ($profileData['asset_id'] && !VehicleProfile::where('asset_id', $profileData['asset_id'])->exists()) {
                VehicleProfile::create($profileData);
            }
        }
    }
}