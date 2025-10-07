<?php

namespace App\Console\Commands;

use App\Models\VehicleTaxHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Check for vehicle tax histories that are due within 3 months and create new VehicleTaxType records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting vehicle tax due date check...');

        $threeMonthsFromNow = Carbon::now()->addMonths(3);
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
            ->chunk(100, function ($histories) use ($threeMonthsFromNow, &$createdCount) {
                $filtered = $histories->filter(function ($history) use ($threeMonthsFromNow) {
                    return $history->vehicleTaxType && $history->due_date->lte($threeMonthsFromNow);
                });

                foreach ($filtered as $history) {
                    $createdCount++;
                    VehicleTaxHistory::createFromTaxType($history->vehicleTaxType);
                    // proses data terbaru
                }
            });

        $this->info("Vehicle tax due date check completed. Created {$createdCount} new VehicleTaxType records.");

        return Command::SUCCESS;
    }
}
