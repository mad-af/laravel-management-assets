<?php

namespace Database\Seeders;

use App\Enums\VehicleTaxTypeEnum;
use App\Models\VehicleTaxHistory;
use App\Models\VehicleTaxType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VehicleTaxHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all vehicle tax types
        $vehicleTaxTypes = VehicleTaxType::all();

        if ($vehicleTaxTypes->count() === 0) {
            return;
        }

        foreach ($vehicleTaxTypes as $taxType) {
            // Create payment history based on tax type
            if ($taxType->tax_type === VehicleTaxTypeEnum::PKB_TAHUNAN) {
                // PKB Tahunan - Create payment for previous year and current year
                $this->createPkbHistory($taxType);
            } elseif ($taxType->tax_type === VehicleTaxTypeEnum::KIR) {
                // KIR - Create payment for current period
                $this->createKirHistory($taxType);
            }
        }
    }

    /**
     * Create PKB Tahunan payment history
     */
    private function createPkbHistory(VehicleTaxType $taxType): void
    {
        // Previous year payment (2023)
        if (!VehicleTaxHistory::where('vehicle_tax_type_id', $taxType->id)
            ->where('year', 2023)
            ->exists()) {
            
            VehicleTaxHistory::create([
                'vehicle_tax_type_id' => $taxType->id,
                'asset_id' => $taxType->asset_id,
                'paid_date' => Carbon::create(2023, 12, 15),
                'due_date' => Carbon::create(2023, 12, 31),
                'year' => 2023,
                'amount' => 1500000.00,
                'receipt_no' => 'PKB-2023-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Pembayaran PKB Tahunan 2023',
            ]);
        }

        // Current year payment (2024) - some paid, some not
        if (!VehicleTaxHistory::where('vehicle_tax_type_id', $taxType->id)
            ->where('year', 2024)
            ->exists()) {
            
            // 70% chance of being paid
            if (rand(1, 10) <= 7) {
                VehicleTaxHistory::create([
                    'vehicle_tax_type_id' => $taxType->id,
                    'asset_id' => $taxType->asset_id,
                    'paid_date' => Carbon::create(2024, rand(1, 10), rand(1, 28)),
                    'due_date' => Carbon::create(2024, 12, 31),
                    'year' => 2024,
                    'amount' => rand(1400000, 1800000),
                    'receipt_no' => 'PKB-2024-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'notes' => 'Pembayaran PKB Tahunan 2024',
                ]);
            }
        }
    }

    /**
     * Create KIR payment history
     */
    private function createKirHistory(VehicleTaxType $taxType): void
    {
        // Previous KIR payment (6 months ago)
        if (!VehicleTaxHistory::where('vehicle_tax_type_id', $taxType->id)
            ->where('year', 2024)
            ->whereMonth('paid_date', '<=', 6)
            ->exists()) {
            
            VehicleTaxHistory::create([
                'vehicle_tax_type_id' => $taxType->id,
                'asset_id' => $taxType->asset_id,
                'paid_date' => Carbon::create(2024, 5, rand(1, 28)),
                'due_date' => Carbon::create(2024, 5, 31),
                'year' => 2024,
                'amount' => rand(200000, 350000),
                'receipt_no' => 'KIR-2024-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Pembayaran KIR Periode Mei 2024',
            ]);
        }

        // Current KIR payment (some paid, some not)
        if (!VehicleTaxHistory::where('vehicle_tax_type_id', $taxType->id)
            ->where('year', 2024)
            ->whereMonth('paid_date', '>=', 7)
            ->exists()) {
            
            // 60% chance of being paid
            if (rand(1, 10) <= 6) {
                VehicleTaxHistory::create([
                    'vehicle_tax_type_id' => $taxType->id,
                    'asset_id' => $taxType->asset_id,
                    'paid_date' => Carbon::create(2024, rand(7, 10), rand(1, 28)),
                    'due_date' => Carbon::create(2024, 11, 15),
                    'year' => 2024,
                    'amount' => rand(200000, 350000),
                    'receipt_no' => 'KIR-2024-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'notes' => 'Pembayaran KIR Periode November 2024',
                ]);
            }
        }
    }
}