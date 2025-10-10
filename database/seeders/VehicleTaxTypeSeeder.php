<?php

namespace Database\Seeders;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleTaxType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VehicleTaxTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get vehicle category
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        
        if (!$vehicleCategory) {
            return;
        }

        // Get all vehicle assets that have profiles
        $vehicleAssets = Asset::where('category_id', $vehicleCategory->id)
            ->whereHas('vehicleProfile')
            ->get();

        if ($vehicleAssets->count() === 0) {
            return;
        }

        // Create tax types for each vehicle
        foreach ($vehicleAssets as $asset) {
            // PKB Tahunan (Annual Vehicle Tax)
            $pkbDueDate = Carbon::create(2024, 12, 31); // End of year
            if (!VehicleTaxType::where('asset_id', $asset->id)
                ->where('tax_type', VehicleTaxTypeEnum::PKB_TAHUNAN)
                ->exists()) {
                
                VehicleTaxType::create([
                    'asset_id' => $asset->id,
                    'tax_type' => VehicleTaxTypeEnum::PKB_TAHUNAN,
                    'due_date' => $pkbDueDate,
                ]);
            }

            // KIR (Vehicle Inspection)
            $kirDueDate = Carbon::create(2024, 11, 15); // Mid November
            if (!VehicleTaxType::where('asset_id', $asset->id)
                ->where('tax_type', VehicleTaxTypeEnum::KIR)
                ->exists()) {
                
                VehicleTaxType::create([
                    'asset_id' => $asset->id,
                    'tax_type' => VehicleTaxTypeEnum::KIR,
                    'due_date' => $kirDueDate,
                ]);
            }
        }
    }
}