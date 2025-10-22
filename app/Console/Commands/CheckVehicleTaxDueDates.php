<?php

namespace App\Console\Commands;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\VehicleTaxHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckVehicleTaxDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle-tax:check-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for vehicle tax histories that are due within 1 month and create new VehicleTaxType records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('vehicle-tax:check-due-dates started', ['timestamp' => now()->toDateTimeString()]);
        $this->info('Starting vehicle tax due date check...');

        try {
            $oneMonthFromNow = Carbon::now()->addMonth();
            $createdCount = 0;

            // Get the latest VehicleTaxHistory for each vehicle_tax_type_id based on created_at
            $latestHistoryIds = VehicleTaxHistory::select('id')
                ->whereIn('id', function ($query) {
                    $query->select(DB::raw('id'))
                        ->from('vehicle_tax_histories as vth1')
                        ->whereRaw('vth1.due_date = (
                            SELECT MAX(vth2.due_date) 
                            FROM vehicle_tax_histories as vth2 
                            WHERE vth2.vehicle_tax_type_id = vth1.vehicle_tax_type_id
                        )');
                });

            VehicleTaxHistory::with('vehicleTaxType')
                ->whereIn('id', $latestHistoryIds)
                ->chunk(100, function ($histories) use ($oneMonthFromNow, &$createdCount) {
                    $filtered = $histories->filter(callback: function ($history) use ($oneMonthFromNow) {
                        // Calculate the next due date based on tax type and latest history
                        $nextDueDate = $history->due_date;
                        $baseDate = $history->due_date;
                        if ($history->vehicleTaxType->tax_type === VehicleTaxTypeEnum::PKB_TAHUNAN) {
                            $nextDueDate = Carbon::parse($baseDate)->addYear();
                        } elseif ($history->vehicleTaxType->tax_type === VehicleTaxTypeEnum::KIR) {
                            $nextDueDate = Carbon::parse($baseDate)->addMonths(6);
                        }

                        return $history->vehicleTaxType && $nextDueDate->lte($oneMonthFromNow);
                    });
                    foreach ($filtered as $history) {
                        $createdCount++;
                        VehicleTaxHistory::createFromTaxType($history->vehicleTaxType);
                    }
                });

            Log::info('vehicle-tax:check-due-dates completed', ['created_count' => $createdCount]);
            $this->info("Vehicle tax due date check completed. Created {$createdCount} new VehicleTaxType records.");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('vehicle-tax:check-due-dates failed', [
                'timestamp' => now()->toDateTimeString(),
                'message' => $e->getMessage(),
            ]);
            $this->error('Vehicle tax due date check failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
