<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'id' => Str::uuid(),
                'name' => 'PT Teknologi Maju Indonesia',
                'code' => 'TMI',
                'tax_id' => '01.234.567.8-901.000',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
                'phone' => '+62-21-5551234',
                'email' => 'info@teknologimaju.co.id',
                'website' => 'https://teknologimaju.co.id',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CV Solusi Digital Nusantara',
                'code' => 'SDN',
                'tax_id' => '02.345.678.9-012.000',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung, Jawa Barat 40123',
                'phone' => '+62-22-7778899',
                'email' => 'contact@solusidigitak.co.id',
                'website' => 'https://solusidigitak.co.id',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'PT Inovasi Bisnis Terpadu',
                'code' => 'IBT',
                'tax_id' => '03.456.789.0-123.000',
                'address' => 'Jl. Ahmad Yani No. 789, Surabaya, Jawa Timur 60234',
                'phone' => '+62-31-3334455',
                'email' => 'hello@inovasibisnis.co.id',
                'website' => 'https://inovasibisnis.co.id',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}