<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        if ($companies->count() === 0) {
            return;
        }

        $branches = [
            // PT Teknologi Maju Indonesia
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'name' => 'Kantor Pusat Jakarta',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'name' => 'Cabang Bandung',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'name' => 'Cabang Surabaya',
                'is_active' => true,
            ],
            
            // CV Solusi Digital Nusantara
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'name' => 'Kantor Pusat Bandung',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'name' => 'Cabang Jakarta',
                'is_active' => true,
            ],
            
            // PT Inovasi Bisnis Terpadu
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'name' => 'Kantor Pusat Surabaya',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'name' => 'Cabang Malang',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}