<?php

namespace Database\Seeders;

use App\Enums\InsurancePolicyType;
use App\Enums\InsuranceStatus;
use App\Models\Asset;
use App\Models\Insurance;
use App\Models\InsurancePolicy;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InsurancePolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::query()->inRandomOrder()->take(4)->get();
        $insurances = Insurance::all();

        if ($assets->isEmpty() || $insurances->isEmpty()) {
            return; // Require assets and insurances
        }

        $policyCounter = 1000;

        foreach ($assets as $i => $asset) {
            // Ambil asuransi secara acak
            $insuranceActive = $insurances->random();
            $insuranceInactive = $insurances->random();

            if ($i != 0) {
                // --- Polis Aktif ---
                $startActive = Carbon::now()->subMonths(rand(1, 12))->startOfDay();
                $endActive = $startActive->copy()->addMonths(rand(6, 18))->endOfDay();

                InsurancePolicy::create([
                    'id' => Str::uuid(),
                    'asset_id' => $asset->id,
                    'insurance_id' => $insuranceActive->id,
                    'policy_no' => 'POL-'.$policyCounter++,
                    'policy_type' => collect(InsurancePolicyType::cases())->random()->value,
                    'start_date' => $startActive,
                    'end_date' => $endActive,
                    'status' => InsuranceStatus::ACTIVE->value,
                    'notes' => 'Polis aktif hasil seeder',
                ]);
            }

            // --- Polis Tidak Aktif (riwayat) ---
            $startInactive = Carbon::now()->subMonths(rand(12, 36))->startOfDay();
            $endInactive = $startInactive->copy()->addMonths(rand(6, 12))->endOfDay();

            InsurancePolicy::create([
                'id' => Str::uuid(),
                'asset_id' => $asset->id,
                'insurance_id' => $insuranceInactive->id,
                'policy_no' => 'POL-'.$policyCounter++,
                'policy_type' => collect(InsurancePolicyType::cases())->random()->value,
                'start_date' => $startInactive,
                'end_date' => $endInactive,
                'status' => InsuranceStatus::INACTIVE->value,
                'notes' => 'Polis tidak aktif (riwayat) hasil seeder',
            ]);
        }

        // foreach ($assets as $asset) {
        //     $insurance = $insurances->random();

        //     $time = Carbon::now()->subMonths(rand(1, 18))->startOfDay();

        //     // Create one active policy per asset
        //     InsurancePolicy::create([
        //         'id' => Str::uuid(),
        //         'asset_id' => $asset->id,
        //         'insurance_id' => $insurance->id,
        //         'policy_no' => 'POL-'.$policyCounter++,
        //         'policy_type' => collect(InsurancePolicyType::cases())->random()->value,
        //         'start_date' => $time,
        //         'end_date' => $time->copy()->addMonths(rand(1, 18))->endOfDay(),
        //         'status' => InsuranceStatus::ACTIVE->value,
        //         'notes' => 'Polis aktif hasil seeder',
        //     ]);

        //     // Occasionally add an older inactive policy for the same asset
        //     if (rand(0, 1) === 1) {
        //         $time = Carbon::now()->subMonths(rand(1, 18))->startOfDay();
        //         InsurancePolicy::create([
        //             'id' => Str::uuid(),
        //             'asset_id' => $asset->id,
        //             'insurance_id' => $insurances->random()->id,
        //             'policy_no' => 'POL-'.$policyCounter++,
        //             'policy_type' => collect(InsurancePolicyType::cases())->random()->value,
        //             'start_date' => $time,
        //             'end_date' => $time->copy()->addMonths(rand(12, 36))->endOfDay(),
        //             'status' => InsuranceStatus::INACTIVE->value,
        //             'notes' => 'Polis tidak aktif (riwayat) hasil seeder',
        //         ]);
        //     }
        // }
    }
}
