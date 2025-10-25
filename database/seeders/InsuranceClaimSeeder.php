<?php

namespace Database\Seeders;

use App\Enums\InsuranceClaimIncidentType;
use App\Enums\InsuranceClaimSource;
use App\Enums\InsuranceClaimStatus;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InsuranceClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = InsurancePolicy::query()->inRandomOrder()->take(20)->get();
        if ($policies->isEmpty()) {
            return; // Require policies
        }

        $userId = User::query()->value('id');
        $claimCounter = 2000;

        foreach ($policies as $policy) {
            // Create 1-2 claims per selected policy
            $count = rand(1, 2);
            for ($i = 0; $i < $count; $i++) {
                InsuranceClaim::create([
                    'id' => Str::uuid(),
                    'policy_id' => $policy->id,
                    'asset_id' => $policy->asset_id,
                    'claim_no' => 'CLM-' . $claimCounter++,
                    'incident_date' => Carbon::now()->subDays(rand(10, 180))->startOfDay(),
                    'incident_type' => collect(InsuranceClaimIncidentType::cases())->random()->value,
                    'incident_other' => null,
                    'description' => 'Klaim hasil seeder',
                    'source' => collect(InsuranceClaimSource::cases())->random()->value,
                    'asset_maintenance_id' => null,
                    'status' => collect(InsuranceClaimStatus::cases())->filter(fn ($item) => $item !== InsuranceClaimStatus::DRAFT)->random()->value,
                    'claim_documents' => [],
                    'amount_approved' => rand(1000000, 10000000),
                    'amount_paid' => rand(0, 10000000),
                    'created_by' => $userId,
                ]);
            }
        }
    }
}