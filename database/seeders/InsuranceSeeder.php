<?php

namespace Database\Seeders;

use App\Models\Insurance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insurances = [
            [
                'id' => Str::uuid(),
                'name' => 'PT Asuransi Jiwa Prudential Indonesia',
                'phone' => '+62-21-29545555',
                'email' => 'corporate@prudential.co.id',
                'address' => 'Plaza Kuningan, Jl. HR Rasuna Said Kav 1, Jakarta',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'PT AIA Financial Indonesia',
                'phone' => '+62-21-XXXYYYYY',
                'email' => 'business@aia-financial.co.id',
                'address' => 'Menara BCA, Jl. M.H. Thamrin No. 1, Jakarta',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'PT AXA Mandiri Financial Services',
                'phone' => '+62-21-XXXZZZZZ',
                'email' => 'corp@axa-mandiri.co.id',
                'address' => 'Gedung AXA, Jl. Jend. Sudirman Kav 45, Jakarta',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'PT Asuransi Allianz Life Indonesia',
                'phone' => '+62-21-2926-8888',
                'email' => 'contactus@allianz.co.id',
                'address' => 'World Trade Centre (WTC) 3 & 6, Jl. Jenderal Sudirman Kav. 29-31, Jakarta Selatan 12920',
            ],

        ];

        foreach ($insurances as $insurance) {
            Insurance::create($insurance);
        }
    }
}
