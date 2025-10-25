<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => Str::uuid(),
                'name' => 'Komputer & Laptop',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Peralatan Kantor',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Kendaraan',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Furniture',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Elektronik',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Peralatan Keamanan',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Alat Komunikasi',
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Peralatan Maintenance',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            // Idempotent seeding: upsert by unique name
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'is_active' => $category['is_active'],
                ]
            );
        }
    }
}
