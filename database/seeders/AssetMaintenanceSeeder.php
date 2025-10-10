<?php

namespace Database\Seeders;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssetMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all assets
        $assets = Asset::with(['category', 'vehicleProfile'])->get();

        // Get employees for assignment
        $employees = Employee::all();

        if ($assets->count() === 0 || $employees->count() === 0) {
            return;
        }

        $maintenanceRecords = [];

        foreach ($assets as $asset) {
            // Create 1-3 maintenance records per asset
            $recordCount = rand(1, 3);

            for ($i = 0; $i < $recordCount; $i++) {
                $isVehicle = $asset->category && $asset->category->name === 'Kendaraan';
                $employee = $employees->random();

                // Generate maintenance data based on asset type
                if ($isVehicle) {
                    $maintenanceRecords[] = $this->generateVehicleMaintenance($asset, $employee, $i);
                } else {
                    $maintenanceRecords[] = $this->generateGeneralMaintenance($asset, $employee, $i);
                }
            }
        }

        // Create maintenance records
        foreach ($maintenanceRecords as $record) {
            if (! AssetMaintenance::where('asset_id', $record['asset_id'])
                ->where('title', $record['title'])
                ->exists()) {

                AssetMaintenance::create($record);
            }
        }
    }

    /**
     * Generate vehicle-specific maintenance record
     */
    private function generateVehicleMaintenance(Asset $asset, Employee $employee, int $index): array
    {
        $vehicleMaintenanceTypes = [
            'Service Rutin',
            'Ganti Oli Mesin',
            'Tune Up',
            'Ganti Ban',
            'Service AC',
            'Perbaikan Rem',
            'Ganti Filter Udara',
            'Service Transmisi',
        ];

        $serviceTasks = [
            ['task' => 'Ganti oli mesin', 'completed' => true],
            ['task' => 'Cek tekanan ban', 'completed' => true],
            ['task' => 'Periksa sistem rem', 'completed' => rand(0, 1) === 1],
            ['task' => 'Ganti filter udara', 'completed' => rand(0, 1) === 1],
            ['task' => 'Cek aki dan sistem kelistrikan', 'completed' => true],
        ];

        $title = $vehicleMaintenanceTypes[array_rand($vehicleMaintenanceTypes)];
        $startedAt = Carbon::now()->subDays(rand(30, 180));
        $status = $this->getRandomStatus($startedAt);

        $currentOdometer = $asset->vehicleProfile?->current_odometer_km ?? rand(20000, 80000);
        $serviceOdometer = $currentOdometer - rand(1000, 5000);

        return [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'title' => $title,
            'type' => rand(0, 1) ? MaintenanceType::PREVENTIVE : MaintenanceType::CORRECTIVE,
            'status' => $status,
            'priority' => $this->getRandomPriority(),
            'started_at' => $startedAt,
            'estimated_completed_at' => $startedAt->copy()->addDays(rand(1, 7)),
            'completed_at' => $status === MaintenanceStatus::COMPLETED ? $startedAt->copy()->addDays(rand(1, 5)) : null,
            'cost' => rand(500000, 2000000),
            'technician_name' => 'Teknisi '.['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko'][array_rand(['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko'])],
            'vendor_name' => ['Bengkel Jaya', 'Auto Service Center', 'Mitra Otomotif', 'Garasi Prima'][array_rand(['Bengkel Jaya', 'Auto Service Center', 'Mitra Otomotif', 'Garasi Prima'])],
            'notes' => 'Maintenance '.strtolower($title).' sesuai jadwal',
            'odometer_km_at_service' => $serviceOdometer,
            'next_service_target_odometer_km' => $serviceOdometer + rand(5000, 10000),
            'next_service_date' => $startedAt->copy()->addMonths(rand(3, 6)),
            'invoice_no' => 'INV-'.date('Y', $startedAt->timestamp).'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'service_tasks' => $serviceTasks,
        ];
    }

    /**
     * Generate general asset maintenance record
     */
    private function generateGeneralMaintenance(Asset $asset, Employee $employee, int $index): array
    {
        $generalMaintenanceTypes = [
            'Pembersihan Rutin',
            'Kalibrasi Perangkat',
            'Penggantian Komponen',
            'Perbaikan Hardware',
            'Update Software',
            'Perawatan Preventif',
            'Inspeksi Berkala',
        ];

        $title = $generalMaintenanceTypes[array_rand($generalMaintenanceTypes)];
        $startedAt = Carbon::now()->subDays(rand(30, 180));
        $status = $this->getRandomStatus($startedAt);

        return [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'title' => $title,
            'type' => rand(0, 1) ? MaintenanceType::PREVENTIVE : MaintenanceType::CORRECTIVE,
            'status' => $status,
            'priority' => $this->getRandomPriority(),
            'started_at' => $startedAt,
            'estimated_completed_at' => $startedAt->copy()->addDays(rand(1, 5)),
            'completed_at' => $status === MaintenanceStatus::COMPLETED ? $startedAt->copy()->addDays(rand(1, 3)) : null,
            'cost' => rand(100000, 1000000),
            'technician_name' => 'Teknisi '.['Fajar', 'Gilang', 'Hendra', 'Indra', 'Joko'][array_rand(['Fajar', 'Gilang', 'Hendra', 'Indra', 'Joko'])],
            'vendor_name' => ['Service Center', 'Tech Support', 'Maintenance Pro', 'Expert Care'][array_rand(['Service Center', 'Tech Support', 'Maintenance Pro', 'Expert Care'])],
            'notes' => 'Maintenance '.strtolower($title).' untuk '.$asset->name,
            'invoice_no' => 'INV-'.date('Y', $startedAt->timestamp).'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * Get random maintenance status based on start date
     */
    private function getRandomStatus(Carbon $startedAt): MaintenanceStatus
    {
        $daysSinceStart = $startedAt->diffInDays(now());

        if ($daysSinceStart > 30) {
            // Older maintenance is more likely to be completed
            return rand(1, 10) <= 8 ? MaintenanceStatus::COMPLETED : MaintenanceStatus::CANCELLED;
        } elseif ($daysSinceStart > 7) {
            // Recent maintenance might be in progress or completed
            $statuses = [MaintenanceStatus::COMPLETED, MaintenanceStatus::IN_PROGRESS];

            return $statuses[array_rand($statuses)];
        } else {
            // Very recent maintenance might still be open or in progress
            $statuses = [MaintenanceStatus::OPEN, MaintenanceStatus::IN_PROGRESS];

            return $statuses[array_rand($statuses)];
        }
    }

    /**
     * Get random maintenance priority
     */
    private function getRandomPriority(): MaintenancePriority
    {
        $priorities = [
            MaintenancePriority::LOW,
            MaintenancePriority::MEDIUM,
            MaintenancePriority::HIGH,
        ];

        // Weight towards medium priority
        $weights = [3, 5, 2]; // LOW, MEDIUM, HIGH
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($weights as $index => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $priorities[$index];
            }
        }

        return MaintenancePriority::MEDIUM;
    }
}
