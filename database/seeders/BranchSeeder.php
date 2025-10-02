<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    public array $hqBranchIds = [
        'TMI' => '550e8400-e29b-41d4-a716-446655440001', // Kantor Pusat Jakarta
        'SDN' => '550e8400-e29b-41d4-a716-446655440004', // Kantor Pusat Bandung
        'IBT' => '550e8400-e29b-41d4-a716-446655440006', // Kantor Pusat Surabaya
    ];

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
                'id' => $this->hqBranchIds['TMI'], // Fixed UUID for HQ
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
                'id' => $this->hqBranchIds['SDN'], // Fixed UUID for HQ
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
                'id' => $this->hqBranchIds['IBT'], // Fixed UUID for HQ
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
