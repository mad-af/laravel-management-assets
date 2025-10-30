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
use Faker\Factory as Faker;

class AssetSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $companies = Company::all();
        $categories = Category::all();
        $branches = Branch::all();

        if ($companies->count() === 0 || $categories->count() === 0 || $branches->count() === 0) {
            $this->command->warn('❗ Data Company, Category, atau Branch masih kosong. Jalankan seeder terkait dulu.');
            return;
        }

        $conditions = [
            AssetCondition::GOOD,
            AssetCondition::FAIR,
            AssetCondition::POOR,
        ];

        $statuses = [
            AssetStatus::ACTIVE,
            AssetStatus::ON_LOAN,
            AssetStatus::INACTIVE,
            AssetStatus::MAINTENANCE,
        ];

        $brands = ['HP', 'Dell', 'Lenovo', 'ASUS', 'Acer', 'Canon', 'Epson', 'Brother', 'LG', 'Panasonic', 'Daikin', 'Sharp', 'Toyota', 'Honda', 'Suzuki'];
        $models = ['Latitude 5520', 'ThinkPad E14', 'EliteBook 840', 'VivoBook 14', 'Swift 3', 'ImageClass MF445dw', 'EcoTank L3250', 'FTV35AXV14', 'BR-S600', 'Innova 2.0 G', 'Civic RS', 'Carry PickUp'];
        $descriptions = ['Peralatan kantor', 'Laptop karyawan', 'Perangkat meeting room', 'Printer dokumen', 'Kendaraan operasional', 'Peralatan IT', 'AC ruang kerja', 'Monitor dan aksesoris'];

        $assetData = [];

        for ($i = 1; $i <= 500; $i++) {
            $company = $companies->random();
            $category = $categories->random();
            $branch = $branches->random();

            $asset = [
                'id' => Str::uuid(),
                'company_id' => $company->id,
                'name' => ucfirst($faker->randomElement(['Laptop', 'Printer', 'Monitor', 'Kursi', 'Meja', 'AC', 'Mobil', 'Motor', 'Proyektor', 'Server'])) . ' ' . $faker->randomElement($brands),
                'category_id' => $category->id,
                'branch_id' => $branch->id,
                'brand' => $faker->randomElement($brands),
                'model' => $faker->randomElement($models),
                'status' => $faker->randomElement($statuses),
                'condition' => $faker->randomElement($conditions),
                'value' => $faker->numberBetween(1000000, 250000000),
                'purchase_date' => $faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
                'description' => $faker->randomElement($descriptions),
                'code' => '', // nanti diisi di bawah
                'tag_code' => '', // nanti diisi di bawah
            ];

            $asset['code'] = now()->format('YmdHis').Str::random(6).$i;
            $asset['tag_code'] = now()->format('YmdHis').Str::random(6).$i;

            $assetData[] = $asset;
        }

        $chunks = array_chunk($assetData, 100);
        foreach ($chunks as $chunk) {
            Asset::insert($chunk);
        }

        $this->command->info('✅ Seeder berhasil: 500 data aset acak telah dimasukkan.');
    }
}
