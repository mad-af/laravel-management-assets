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
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'name' => 'Cabang Bandung',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung, Jawa Barat 40123',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'name' => 'Cabang Surabaya',
                'address' => 'Jl. Ahmad Yani No. 789, Surabaya, Jawa Timur 60234',
                'is_active' => true,
            ],
            
            // CV Solusi Digital Nusantara
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'name' => 'Kantor Pusat Bandung',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung, Jawa Barat 40123',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'name' => 'Cabang Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
                'is_active' => true,
            ],
            
            // PT Inovasi Bisnis Terpadu
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'name' => 'Kantor Pusat Surabaya',
                'address' => 'Jl. Ahmad Yani No. 789, Surabaya, Jawa Timur 60234',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'name' => 'Cabang Malang',
                'address' => 'Jl. Gatot Subroto No. 456, Malang, Jawa Timur 60234',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}