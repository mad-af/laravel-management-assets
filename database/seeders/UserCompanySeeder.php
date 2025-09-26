<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\UserCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $companies = Company::all();
        
        if ($users->count() === 0 || $companies->count() === 0) {
            return;
        }

        $userCompanies = [
            // Admin user - access to all companies
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'admin@example.com')->first()->id,
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'company_role' => 'admin',
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'admin@example.com')->first()->id,
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'company_role' => 'admin',
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'admin@example.com')->first()->id,
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'company_role' => 'admin',
            ],
            
            // TMI users
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'budi.santoso@teknologimaju.co.id')->first()->id,
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'company_role' => 'admin',
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'siti.nurhaliza@teknologimaju.co.id')->first()->id,
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'company_role' => 'staff',
            ],
            
            // SDN users
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'ahmad.wijaya@solusidigitak.co.id')->first()->id,
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'company_role' => 'admin',
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'dewi.lestari@solusidigitak.co.id')->first()->id,
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'company_role' => 'staff',
            ],
            
            // IBT users
            [
                'id' => Str::uuid(),
                'user_id' => $users->where('email', 'rudi.hartono@inovasibisnis.co.id')->first()->id,
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'company_role' => 'admin',
            ],
        ];

        foreach ($userCompanies as $userCompany) {
            UserCompany::create($userCompany);
        }
    }
}