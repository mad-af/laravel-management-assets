<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssetLoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find assets currently marked as ON_LOAN
        $assetsOnLoan = Asset::where('status', AssetStatus::ON_LOAN)->get();
        $employees = Employee::all();

        if ($assetsOnLoan->count() === 0 || $employees->count() === 0) {
            return;
        }

        foreach ($assetsOnLoan as $asset) {
            // Skip if an active loan already exists for this asset
            $hasActiveLoan = AssetLoan::where('asset_id', $asset->id)
                ->whereNull('checkin_at')
                ->exists();

            if ($hasActiveLoan) {
                continue;
            }

            // Prefer employee from same branch, then same company, else random
            $employee = Employee::where('branch_id', $asset->branch_id)->first()
                ?? Employee::where('company_id', $asset->company_id)->first()
                ?? $employees->random();

            AssetLoan::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'checkout_at' => Carbon::now()->subDays(rand(1, 10)),
                'due_at' => Carbon::now()->addDays(rand(5, 14)),
                'checkin_at' => null,
                'condition_out' => $asset->condition ?? AssetCondition::GOOD,
                'condition_in' => null,
                'notes' => 'Seeded active loan for '.$asset->name,
            ]);
        }
    }
}
