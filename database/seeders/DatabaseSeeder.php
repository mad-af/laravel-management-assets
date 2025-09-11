<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use App\Models\AssetLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use App\Enums\AssetStatus;
use App\Enums\AssetCondition;
use App\Enums\AssetLogAction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Create categories
        $categories = [
            ['name' => 'Komputer & Laptop', 'is_active' => true],
            ['name' => 'Furniture', 'is_active' => true],
            ['name' => 'Kendaraan', 'is_active' => true],
            ['name' => 'Elektronik', 'is_active' => true],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create locations
        $locations = [
            ['name' => 'Kantor Pusat', 'is_active' => true],
            ['name' => 'Gudang', 'is_active' => true],
            ['name' => 'Cabang Jakarta', 'is_active' => true],
            ['name' => 'Cabang Surabaya', 'is_active' => true],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        // Get created categories and locations
        $computerCategory = Category::where('name', 'Komputer & Laptop')->first();
        $furnitureCategory = Category::where('name', 'Furniture')->first();
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        
        $officeLocation = Location::where('name', 'Kantor Pusat')->first();
        $warehouseLocation = Location::where('name', 'Gudang')->first();
        $jakartaLocation = Location::where('name', 'Cabang Jakarta')->first();

        // Create assets
        $assets = [
            [
                'code' => 'COMP-001',
                'name' => 'Laptop Dell Inspiron 15',
                'category_id' => $computerCategory->id,
                'location_id' => $officeLocation->id,
                'status' => AssetStatus::ACTIVE,
                'condition' => 'excellent',
                'value' => 8500000.00,
                'purchase_date' => '2024-01-15',
                'description' => 'Laptop untuk karyawan IT',
            ],
            [
                'code' => 'FURN-001',
                'name' => 'Meja Kerja Kayu',
                'category_id' => $furnitureCategory->id,
                'location_id' => $officeLocation->id,
                'status' => 'active',
                'condition' => 'good',
                'value' => 1500000.00,
                'purchase_date' => '2023-12-01',
                'description' => 'Meja kerja untuk ruang meeting',
            ],
            [
                'code' => 'VEH-001',
                'name' => 'Toyota Avanza 2023',
                'category_id' => $vehicleCategory->id,
                'location_id' => $jakartaLocation->id,
                'status' => 'active',
                'condition' => 'excellent',
                'value' => 220000000.00,
                'purchase_date' => '2023-08-10',
                'description' => 'Kendaraan operasional cabang Jakarta',
            ],
            [
                'code' => 'COMP-002',
                'name' => 'PC Desktop HP',
                'category_id' => $computerCategory->id,
                'location_id' => $warehouseLocation->id,
                'status' => AssetStatus::MAINTENANCE,
                'condition' => 'fair',
                'value' => 6000000.00,
                'purchase_date' => '2022-05-20',
                'description' => 'PC untuk sistem inventory gudang',
            ],
            [
                'code' => 'ELEC-001',
                'name' => 'Printer Canon Pixma',
                'category_id' => Category::where('name', 'Elektronik')->first()->id,
                'location_id' => $officeLocation->id,
                'status' => 'active',
                'condition' => 'good',
                'value' => 2500000.00,
                'purchase_date' => '2023-11-15',
                'description' => 'Printer untuk keperluan kantor',
            ],
            [
                'code' => 'FURN-002',
                'name' => 'Kursi Ergonomis',
                'category_id' => $furnitureCategory->id,
                'location_id' => $jakartaLocation->id,
                'status' => 'active',
                'condition' => 'excellent',
                'value' => 1200000.00,
                'purchase_date' => '2024-02-01',
                'description' => 'Kursi kerja untuk karyawan',
            ],
        ];

        foreach ($assets as $assetData) {
            $asset = Asset::create($assetData);
            
            // Create asset log for each asset
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $admin->id,
                'action' => AssetLogAction::CREATED,
                'changed_fields' => null,
                'notes' => 'Asset created during database seeding',
            ]);
        }

        // Create additional asset log for maintenance status
        $maintenanceAsset = Asset::where('code', 'COMP-002')->first();
        AssetLog::create([
            'asset_id' => $maintenanceAsset->id,
            'user_id' => $admin->id,
            'action' => AssetLogAction::STATUS_CHANGED,
            'changed_fields' => [
                'status' => ['old' => AssetStatus::ACTIVE->value, 'new' => AssetStatus::MAINTENANCE->value]
            ],
            'notes' => 'Asset moved to maintenance due to hardware issues',
        ]);
    }
}
