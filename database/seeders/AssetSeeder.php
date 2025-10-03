<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $categories = Category::all();
        $branches = Branch::all();

        if ($companies->count() === 0 || $categories->count() === 0 || $branches->count() === 0) {
            return;
        }

        $tmiCompany = $companies->where('code', 'TMI')->first();
        $sdnCompany = $companies->where('code', 'SDN')->first();
        $ibtCompany = $companies->where('code', 'IBT')->first();

        $computerCategory = $categories->where('name', 'Komputer & Laptop')->first();
        $officeCategory = $categories->where('name', 'Peralatan Kantor')->first();
        $vehicleCategory = $categories->where('name', 'Kendaraan')->first();
        $furnitureCategory = $categories->where('name', 'Furniture')->first();
        $electronicCategory = $categories->where('name', 'Elektronik')->first();

        // Get branch IDs
        $jakartaBranchId = $branches->where('name', 'Kantor Pusat Jakarta')->first()->id;
        $bandungBranchId = $branches->where('name', 'Kantor Pusat Bandung')->first()->id;
        $surabayaBranchId = $branches->where('name', 'Kantor Pusat Surabaya')->first()->id;

        $assets = [
            // TMI Assets
            [
                'id' => Str::uuid(),
                'company_id' => $tmiCompany->id,
                'name' => 'Laptop Dell Latitude 5520',
                'category_id' => $computerCategory->id,
                'branch_id' => $jakartaBranchId,
                'brand' => 'Dell',
                'model' => 'Latitude 5520',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 15000000.00,
                'purchase_date' => '2023-01-15',
                'description' => 'Laptop untuk karyawan IT',
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $tmiCompany->id,
                'name' => 'Desktop HP EliteDesk 800',
                'category_id' => $computerCategory->id,
                'branch_id' => $jakartaBranchId,
                'brand' => 'HP',
                'model' => 'EliteDesk 800 G6',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 12000000.00,
                'purchase_date' => '2023-02-20',
                'description' => 'Desktop untuk workstation',
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $tmiCompany->id,
                'name' => 'Printer Canon ImageClass MF445dw',
                'category_id' => $officeCategory->id,
                'branch_id' => $jakartaBranchId,
                'brand' => 'Canon',
                'model' => 'ImageClass MF445dw',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 4500000.00,
                'purchase_date' => '2023-03-10',
                'description' => 'Printer multifungsi untuk kantor',
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $tmiCompany->id,
                'name' => 'Toyota Avanza 1.3 G MT',
                'category_id' => $vehicleCategory->id,
                'branch_id' => $jakartaBranchId,
                'brand' => 'Toyota',
                'model' => 'Avanza 1.3 G MT',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 220000000.00,
                'purchase_date' => '2022-08-15',
                'description' => 'Kendaraan operasional perusahaan',
            ],

            // SDN Assets
            [
                'id' => Str::uuid(),
                'company_id' => $sdnCompany->id,
                'name' => 'Laptop Lenovo ThinkPad E14',
                'category_id' => $computerCategory->id,
                'branch_id' => $bandungBranchId,
                'brand' => 'Lenovo',
                'model' => 'ThinkPad E14 Gen 3',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 13500000.00,
                'purchase_date' => '2023-04-05',
                'description' => 'Laptop untuk developer',
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $sdnCompany->id,
                'name' => 'Meja Kerja Kayu Jati',
                'category_id' => $furnitureCategory->id,
                'branch_id' => $bandungBranchId,
                'brand' => 'Custom',
                'model' => 'Meja Eksekutif',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 3500000.00,
                'purchase_date' => '2023-01-20',
                'description' => 'Meja kerja untuk ruang direktur',
            ],

            // IBT Assets
            [
                'id' => Str::uuid(),
                'company_id' => $ibtCompany->id,
                'name' => 'AC Split Daikin 1.5 PK',
                'category_id' => $electronicCategory->id,
                'branch_id' => $surabayaBranchId,
                'brand' => 'Daikin',
                'model' => 'FTV35AXV14',
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 6500000.00,
                'purchase_date' => '2023-05-12',
                'description' => 'AC untuk ruang meeting',
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $ibtCompany->id,
                'name' => 'Laptop ASUS VivoBook 14',
                'category_id' => $computerCategory->id,
                'branch_id' => $surabayaBranchId,
                'brand' => 'ASUS',
                'model' => 'VivoBook 14 A416',
                'status' => AssetStatus::ON_LOAN,
                'condition' => AssetCondition::GOOD,
                'value' => 8500000.00,
                'purchase_date' => '2023-06-18',
                'description' => 'Laptop untuk karyawan administrasi',
            ],
        ];

        foreach ($assets as $asset) {
            // Generate unique codes for each asset
            $asset['code'] = generate_asset_code($asset['category_id'], $asset['branch_id']);
            $asset['tag_code'] = generate_asset_tag_code();

            Asset::create($asset);
        }
    }
}
