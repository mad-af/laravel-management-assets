<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\VehicleTaxHistory;
use App\Models\VehicleTaxType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTaxTestingSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        if (!$vehicleCategory) {
            $this->command->error('Kendaraan category not found!');
            return;
        }

        $vehicles = Asset::where('category_id', $vehicleCategory->id)
            ->where('branch_id', '550e8400-e29b-41d4-a716-446655440001')
            ->whereHas('vehicleProfile')
            ->orderBy('code')
            ->get();

        if ($vehicles->isEmpty()) {
            $this->command->error('No vehicles found for Kantor Pusat Jakarta!');
            return;
        }

        $this->command->info("Found {$vehicles->count()} vehicles. Creating 100 random vehicle tax records...");

        $targetCount = 100;
        $created = 0;

        while ($created < $targetCount) {
            $vehicle = $vehicles->random();
            $taxType = $created % 2 === 0 ? 'pkb_tahunan' : 'kir';
            $label = $taxType === 'pkb_tahunan' ? 'PKB Tahunan' : 'KIR';

            $dueDate = $this->generateRandomDueDate();
            $isPaid = rand(0, 100) < 35;
            $idx = $created + 1;

            $vtt = VehicleTaxType::create([
                'id' => Str::uuid()->toString(),
                'asset_id' => $vehicle->id,
                'tax_type' => $taxType,
                'due_date' => $dueDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($isPaid) {
                VehicleTaxHistory::where('vehicle_tax_type_id', $vtt->id)
                    ->whereDate('due_date', $dueDate)
                    ->update([
                        'paid_date' => Carbon::parse($dueDate)->subDays(rand(1, 60)),
                        'amount' => $this->generateAmount(),
                        'receipt_no' => 'RCP-' . date('Y') . '-' . str_pad($idx, 4, '0', STR_PAD_LEFT),
                        'notes' => 'Paid via seeder',
                    ]);
            }

            $status = $isPaid ? 'PAID' : (Carbon::parse($dueDate)->isPast() ? 'OVERDUE' : (Carbon::parse($dueDate)->diffInDays(now()) <= 30 ? 'DUE SOON' : 'UPCOMING'));
            $this->command->info("  [$idx] {$vehicle->code} - {$label}: {$dueDate} ({$status})");
            $created++;
        }

        $this->command->info('');
        $this->command->info('=== Summary ===');
        $this->command->info('Total vehicle_tax_types: ' . VehicleTaxType::count());
        $this->command->info('Total vehicle_tax_histories: ' . VehicleTaxHistory::count());

        $paidCount = VehicleTaxHistory::whereNotNull('paid_date')->count();
        $overdueCount = VehicleTaxHistory::whereNull('paid_date')->where('due_date', '<', now())->count();
        $dueSoonCount = VehicleTaxHistory::whereNull('paid_date')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->count();
        $upcomingCount = VehicleTaxHistory::whereNull('paid_date')
            ->where('due_date', '>', now()->addDays(30))
            ->count();

        $this->command->info("Paid: {$paidCount}");
        $this->command->info("Overdue: {$overdueCount}");
        $this->command->info("Due Soon (≤30 days): {$dueSoonCount}");
        $this->command->info("Upcoming (>30 days): {$upcomingCount}");
    }

    private function generateRandomDueDate(): string
    {
        $now = Carbon::now();
        $rand = rand(1, 100);

        if ($rand <= 30) {
            return $now->copy()->subDays(rand(1, 180))->format('Y-m-d');
        } elseif ($rand <= 55) {
            return $now->copy()->addDays(rand(1, 30))->format('Y-m-d');
        } else {
            return $now->copy()->addDays(rand(31, 365))->format('Y-m-d');
        }
    }

    private function generateAmount(): float
    {
        return round(rand(50000, 150000) / 1000) * 1000;
    }
}
