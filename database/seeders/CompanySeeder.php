<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default company
        $defaultCompany = Company::create([
            'id' => Str::uuid(),
            'name' => 'Default Company',
            'code' => 'DEFAULT',
            'email' => 'admin@defaultcompany.com',
            'phone' => '+62-21-1234567',
            'website' => 'https://defaultcompany.com',
            'address' => 'Jl. Default No. 1\nJakarta 12345\nIndonesia',
            'is_active' => true,
        ]);

        // Update all existing users with default company_id
        User::whereNull('company_id')->update([
            'company_id' => $defaultCompany->id
        ]);

        // Update all existing categories with default company_id
        Category::whereNull('company_id')->update([
            'company_id' => $defaultCompany->id
        ]);

        // Update all existing locations with default company_id
        Location::whereNull('company_id')->update([
            'company_id' => $defaultCompany->id
        ]);

        // Update all existing assets with default company_id
        Asset::whereNull('company_id')->update([
            'company_id' => $defaultCompany->id
        ]);

        $this->command->info('Default company created and all existing data updated with company_id.');
    }
}
