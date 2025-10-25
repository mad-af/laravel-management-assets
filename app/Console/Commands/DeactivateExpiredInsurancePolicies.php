<?php

namespace App\Console\Commands;

use App\Enums\InsuranceStatus;
use App\Models\InsurancePolicy;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeactivateExpiredInsurancePolicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insurance-policy:deactivate-expired {--dry-run : Only show the count, do not update any records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate insurance policies whose end_date has passed by setting status to inactive.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting expired insurance policy deactivation...');
        Log::info('insurance-policy:deactivate-expired started', ['timestamp' => now()->toDateTimeString()]);

        try {
            $now = Carbon::now()->startOfDay();

            $query = InsurancePolicy::query()
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', $now)
                ->where('status', '!=', InsuranceStatus::INACTIVE->value);

            $total = (clone $query)->count();
            $this->info("Found {$total} policies to deactivate.");

            if ($total === 0) {
                Log::info('insurance-policy:deactivate-expired no records to update');

                return self::SUCCESS;
            }

            $dryRun = (bool) $this->option('dry-run');
            if ($dryRun) {
                $this->warn('Dry run mode: no records will be updated.');

                return self::SUCCESS;
            }

            $updated = 0;
            $query->chunkById(200, function ($policies) use (&$updated) {
                foreach ($policies as $policy) {
                    $policy->status = InsuranceStatus::INACTIVE->value;
                    $policy->save();
                    $updated++;
                }
            });

            Log::info('insurance-policy:deactivate-expired completed', ['updated' => $updated]);
            $this->info("Completed. Updated {$updated} policies to inactive.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('insurance-policy:deactivate-expired failed', [
                'timestamp' => now()->toDateTimeString(),
                'message' => $e->getMessage(),
            ]);
            $this->error('Deactivation failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}