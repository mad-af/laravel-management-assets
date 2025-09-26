<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $branches = Branch::all();
        
        if ($companies->count() === 0 || $branches->count() === 0) {
            return;
        }

        $employees = [
            // TMI Employees
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'branch_id' => $branches->where('name', 'Kantor Pusat Jakarta')->first()->id,
                'employee_number' => 'TMI001',
                'full_name' => 'Andi Prasetyo',
                'email' => 'andi.prasetyo@teknologimaju.co.id',
                'phone' => '+62-812-3456-7890',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'branch_id' => $branches->where('name', 'Kantor Pusat Jakarta')->first()->id,
                'employee_number' => 'TMI002',
                'full_name' => 'Maya Sari',
                'email' => 'maya.sari@teknologimaju.co.id',
                'phone' => '+62-813-4567-8901',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'TMI')->first()->id,
                'branch_id' => $branches->where('name', 'Cabang Bandung')->first()->id,
                'employee_number' => 'TMI003',
                'full_name' => 'Dedi Kurniawan',
                'email' => 'dedi.kurniawan@teknologimaju.co.id',
                'phone' => '+62-814-5678-9012',
                'is_active' => true,
            ],
            
            // SDN Employees
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'branch_id' => $branches->where('name', 'Kantor Pusat Bandung')->first()->id,
                'employee_number' => 'SDN001',
                'full_name' => 'Rina Wulandari',
                'email' => 'rina.wulandari@solusidigitak.co.id',
                'phone' => '+62-815-6789-0123',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'SDN')->first()->id,
                'branch_id' => $branches->where('name', 'Cabang Jakarta')->first()->id,
                'employee_number' => 'SDN002',
                'full_name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@solusidigitak.co.id',
                'phone' => '+62-816-7890-1234',
                'is_active' => true,
            ],
            
            // IBT Employees
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'branch_id' => $branches->where('name', 'Kantor Pusat Surabaya')->first()->id,
                'employee_number' => 'IBT001',
                'full_name' => 'Lina Marlina',
                'email' => 'lina.marlina@inovasibisnis.co.id',
                'phone' => '+62-817-8901-2345',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'company_id' => $companies->where('code', 'IBT')->first()->id,
                'branch_id' => $branches->where('name', 'Cabang Malang')->first()->id,
                'employee_number' => 'IBT002',
                'full_name' => 'Bambang Sutrisno',
                'email' => 'bambang.sutrisno@inovasibisnis.co.id',
                'phone' => '+62-818-9012-3456',
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}