<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\InsurancePolicyType;
use App\Enums\InsuranceStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Enums\VehicleTaxTypeEnum;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMaintenance;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Insurance;
use App\Models\InsurancePolicy;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleProfile;
use App\Models\VehicleTaxHistory;
use App\Models\VehicleTaxType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchRandomTestSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $categories = Category::all();
        $branches = Branch::all();
        $employees = Employee::all();
        $insurances = Insurance::all();

        if ($companies->isEmpty() || $categories->isEmpty() || $branches->isEmpty()) {
            $this->command->warn('Required seeders must run first.');
            return;
        }

        $vehicleCategory = $categories->where('name', 'Kendaraan')->first();
        $computerCategory = $categories->where('name', 'Komputer & Laptop')->first();
        $officeCategory = $categories->where('name', 'Peralatan Kantor')->first();
        $furnitureCategory = $categories->where('name', 'Furniture')->first();
        $electronicCategory = $categories->where('name', 'Elektronik')->first();

        $this->command->info('Creating random test data for ' . $branches->count() . ' branches...');

        foreach ($branches as $branch) {
            $company = $companies->firstWhere('id', $branch->company_id) ?? $companies->first();
            $branchEmployees = $employees->where('branch_id', $branch->id);
            $this->command->info('');
            $this->command->info("=== Branch: {$branch->name} ===");

            $createdAssets = [];
            $assetCount = 0;

            for ($i = 0; $i < 20; $i++) {
                $isVehicle = $i < 8;
                $category = $isVehicle ? $vehicleCategory : $this->randomNonVehicleCategory([
                    $computerCategory, $officeCategory, $furnitureCategory, $electronicCategory
                ]);

                $asset = $this->createAsset($company, $branch, $category, $i);
                $createdAssets[] = $asset;
                $assetCount++;

                if ($isVehicle) {
                    $this->createVehicleProfile($asset, $branch, $i);
                    $this->createVehicleTaxes($asset);
                    $this->createVehicleOdometerLog($asset);
                }

                if ($branchEmployees->isNotEmpty() && rand(0, 100) < 40) {
                    $this->createAssetLoan($asset, $branchEmployees);
                }

                if (rand(0, 100) < 50) {
                    $this->createAssetMaintenance($asset, $branchEmployees);
                }

                if ($insurances->isNotEmpty() && rand(0, 100) < 30) {
                    $this->createInsurancePolicy($asset, $insurances);
                }
            }

            $this->command->info("  Created {$assetCount} assets");
            $this->command->info("  VehicleProfiles: " . VehicleProfile::whereHas('asset', fn($q) => $q->where('branch_id', $branch->id))->count());
            $this->command->info("  VehicleTaxTypes: " . VehicleTaxType::whereHas('asset', fn($q) => $q->where('branch_id', $branch->id))->count());
            $this->command->info("  AssetLoans: " . AssetLoan::whereHas('asset', fn($q) => $q->where('branch_id', $branch->id))->count());
            $this->command->info("  AssetMaintenances: " . AssetMaintenance::whereHas('asset', fn($q) => $q->where('branch_id', $branch->id))->count());
            $this->command->info("  InsurancePolicies: " . InsurancePolicy::whereHas('asset', fn($q) => $q->where('branch_id', $branch->id))->count());
        }

        $this->command->info('');
        $this->command->info('=== TOTALS ===');
        $this->command->info('Assets: ' . Asset::count());
        $this->command->info('VehicleProfiles: ' . VehicleProfile::count());
        $this->command->info('VehicleTaxTypes: ' . VehicleTaxType::count());
        $this->command->info('VehicleTaxHistories: ' . VehicleTaxHistory::count());
        $this->command->info('AssetLoans: ' . AssetLoan::count());
        $this->command->info('AssetMaintenances: ' . AssetMaintenance::count());
        $this->command->info('InsurancePolicies: ' . InsurancePolicy::count());
    }

    private function randomNonVehicleCategory($categories)
    {
        return $categories[array_rand($categories)];
    }

    private function createAsset($company, $branch, $category, int $index): Asset
    {
        $vehicleBrands = ['Toyota', 'Honda', 'Suzuki', 'Mitsubishi', 'Daihatsu', 'Wuling', 'Hyundai'];
        $computerBrands = ['Dell', 'HP', 'Lenovo', 'ASUS', 'Acer', 'Apple'];
        $officeBrands = ['Canon', 'Epson', 'Brother', 'Samsung'];
        $furnitureBrands = ['Custom', 'IKEA', 'Olympic', 'Goodwill'];
        $electronicBrands = ['Daikin', 'Panasonic', 'Sharp', 'LG', 'Samsung'];

        $isVehicle = $category->name === 'Kendaraan';

        $names = [
            'Kendaraan' => [
                'Toyota Avanza 1.3 G MT',
                'Honda Civic 1.5L Turbo',
                'Suzuki Carry 1.5L MT',
                'Mitsubishi L300 Diesel',
                'Daihatsu Xenia 1.3 R MT',
                'Wuling Air EV',
                'Toyota Innova 2.0 V MT',
                'Honda Brio 1.2L MT',
            ],
            'Komputer & Laptop' => [
                'Laptop Dell Latitude 5520',
                'Laptop HP EliteBook 840',
                'Laptop Lenovo ThinkPad E14',
                'Laptop ASUS VivoBook 14',
                'Desktop HP EliteDesk 800 G6',
                'MacBook Pro 14 inch',
                'Laptop Acer Aspire 5',
                'PC Rakitan Core i5',
            ],
            'Peralatan Kantor' => [
                'Printer Canon ImageClass MF445dw',
                'Printer Epson L3250',
                'Mesin Fotokopi Canon IR 2520',
                'Scanner Brother ADS-2700W',
                'Projector Epson EB-980U',
                'Printer HP LaserJet Pro M404dn',
                'Mesin Fax Panasonic KX-FP986',
                'Scanner Fujitsu SP-1425',
            ],
            'Furniture' => [
                'Meja Kerja Kayu Jati',
                'Kursi Ergonomis Herman Miller',
                'Lemari Arsip Steel',
                'Rak Penyimpanan 5 Level',
                'Sofa Tamu 3 Seater',
                'Meja Meeting 6 Orang',
                'Filing Cabinet 4 Laci',
                'Partisi Cubicle Workstation',
            ],
            'Elektronik' => [
                'AC Split Daikin 1.5 PK',
                'AC Split Panasonic 2 PK',
                'TV LED Samsung 43 inch',
                'Dispenser Samsung',
                'Kulkas LG 2 Pintu',
                'Microwave Sharp',
                'Air Purifier Xiaomi',
                'kipas Angin DC Ventila',
            ],
        ];

        $brand = match ($category->name) {
            'Kendaraan' => $vehicleBrands[array_rand($vehicleBrands)],
            'Komputer & Laptop' => $computerBrands[array_rand($computerBrands)],
            'Peralatan Kantor' => $officeBrands[array_rand($officeBrands)],
            'Furniture' => $furnitureBrands[array_rand($furnitureBrands)],
            'Elektronik' => $electronicBrands[array_rand($electronicBrands)],
            default => 'Generic',
        };

        $nameOptions = $names[$category->name] ?? ['Item generik'];
        $name = $nameOptions[$index % count($nameOptions)];

        $value = match ($category->name) {
            'Kendaraan' => rand(150000000, 500000000),
            'Komputer & Laptop' => rand(8000000, 25000000),
            'Peralatan Kantor' => rand(2000000, 15000000),
            'Furniture' => rand(1500000, 25000000),
            'Elektronik' => rand(3000000, 20000000),
            default => rand(1000000, 10000000),
        };

        $statusOptions = [AssetStatus::ACTIVE, AssetStatus::ACTIVE, AssetStatus::ACTIVE, AssetStatus::ON_LOAN, AssetStatus::MAINTENANCE];
        $status = $statusOptions[array_rand($statusOptions)];

        return Asset::create([
            'id' => Str::uuid()->toString(),
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'name' => $name,
            'category_id' => $category->id,
            'brand' => $brand,
            'model' => $brand . ' Model ' . rand(100, 999),
            'code' => generate_asset_code($category->id, $branch->id),
            'tag_code' => generate_asset_tag_code(),
            'status' => $status,
            'condition' => AssetCondition::GOOD,
            'value' => $value,
            'purchase_date' => Carbon::now()->subDays(rand(180, 900))->format('Y-m-d'),
            'description' => "Test asset untuk {$branch->name}",
        ]);
    }

    private function createVehicleProfile(Asset $asset, Branch $branch, int $index): VehicleProfile
    {
        $platePrefixes = ['B', 'D', 'L', 'Z', 'H', 'M'];
        $platePrefix = $platePrefixes[array_rand($platePrefixes)];

        return VehicleProfile::create([
            'asset_id' => $asset->id,
            'year_purchase' => rand(2020, 2024),
            'year_manufacture' => rand(2020, 2024),
            'current_odometer_km' => rand(10000, 120000),
            'last_service_date' => Carbon::now()->subDays(rand(30, 180)),
            'service_target_odometer_km' => rand(50000, 150000),
            'next_service_date' => Carbon::now()->addDays(rand(-30, 120)),
            'plate_no' => $platePrefix . ' ' . rand(1000, 9999) . ' ' . strtoupper(substr($branch->name, 0, 3)),
            'vin' => 'VIN' . str_pad((string) rand(1, 999999999), 9, '0', STR_PAD_LEFT),
        ]);
    }

    private function createVehicleTaxes(Asset $asset): void
    {
        $pkbDue = $this->generateRandomDueDate();
        $kirDue = $this->generateRandomDueDate();

        $pkbTaxType = VehicleTaxType::create([
            'id' => Str::uuid()->toString(),
            'asset_id' => $asset->id,
            'tax_type' => VehicleTaxTypeEnum::PKB_TAHUNAN->value,
            'due_date' => $pkbDue,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kirTaxType = VehicleTaxType::create([
            'id' => Str::uuid()->toString(),
            'asset_id' => $asset->id,
            'tax_type' => VehicleTaxTypeEnum::KIR->value,
            'due_date' => $kirDue,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $isPaid = rand(0, 100) < 35;

        if ($isPaid) {
            VehicleTaxHistory::where('vehicle_tax_type_id', $pkbTaxType->id)
                ->whereDate('due_date', $pkbDue)
                ->update([
                    'paid_date' => Carbon::parse($pkbDue)->subDays(rand(1, 60)),
                    'amount' => $this->generateAmount(),
                    'receipt_no' => 'RCP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'notes' => 'Paid via seeder',
                ]);

            VehicleTaxHistory::where('vehicle_tax_type_id', $kirTaxType->id)
                ->whereDate('due_date', $kirDue)
                ->update([
                    'paid_date' => Carbon::parse($kirDue)->subDays(rand(1, 60)),
                    'amount' => $this->generateAmount(),
                    'receipt_no' => 'RCP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'notes' => 'Paid via seeder',
                ]);
        }
    }

    private function createVehicleOdometerLog(Asset $asset): void
    {
        $profile = $asset->vehicleProfile;
        if (!$profile) return;

        VehicleOdometerLog::create([
            'asset_id' => $asset->id,
            'odometer_km' => $profile->current_odometer_km,
            'read_at' => Carbon::now()->subDays(rand(1, 30)),
            'source' => 'manual',
            'notes' => 'Test odometer log',
        ]);
    }

    private function createAssetLoan(Asset $asset, $employees): void
    {
        if ($employees->isEmpty()) return;

        $employee = $employees->random();
        $checkoutAt = Carbon::now()->subDays(rand(5, 30));
        $dueAt = $checkoutAt->copy()->addDays(rand(7, 30));
        $isOverdue = rand(0, 100) < 20;

        if ($isOverdue) {
            $dueAt = Carbon::now()->subDays(rand(1, 15));
        }

        AssetLoan::create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'checkout_at' => $checkoutAt,
            'due_at' => $dueAt,
            'checkin_at' => null,
            'condition_out' => AssetCondition::GOOD,
            'condition_in' => null,
            'notes' => 'Test loan via seeder',
        ]);
    }

    private function createAssetMaintenance(Asset $asset, $employees): void
    {
        if ($employees->isEmpty()) return;

        $employee = $employees->random();
        $startedAt = Carbon::now()->subDays(rand(10, 120));
        $statusOptions = [MaintenanceStatus::COMPLETED, MaintenanceStatus::COMPLETED, MaintenanceStatus::IN_PROGRESS, MaintenanceStatus::OPEN];
        $status = $statusOptions[array_rand($statusOptions)];

        $titles = $asset->category && $asset->category->name === 'Kendaraan'
            ? ['Service Rutin', 'Ganti Oli', 'Tune Up', 'Service AC', 'Perbaikan Rem', 'Ganti Ban']
            : ['Pembersihan Rutin', 'Kalibrasi', 'Perbaikan Hardware', 'Inspeksi Berkala', 'Update Software'];

        $title = $titles[array_rand($titles)];

        $record = [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'title' => $title,
            'type' => rand(0, 1) ? MaintenanceType::PREVENTIVE : MaintenanceType::CORRECTIVE,
            'status' => $status,
            'priority' => $this->randomPriority(),
            'started_at' => $startedAt,
            'estimated_completed_at' => $startedAt->copy()->addDays(rand(1, 7)),
            'completed_at' => $status === MaintenanceStatus::COMPLETED ? $startedAt->copy()->addDays(rand(1, 5)) : null,
            'cost' => rand(100000, 3000000),
            'technician_name' => 'Teknisi ' . ['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko'][array_rand(['Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko'])],
            'vendor_name' => ['Bengkel Jaya', 'Auto Service', 'Tech Support', 'Expert Care'][array_rand(['Bengkel Jaya', 'Auto Service', 'Tech Support', 'Expert Care'])],
            'notes' => 'Maintenance via seeder',
            'invoice_no' => 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];

        if ($asset->category && $asset->category->name === 'Kendaraan' && $asset->vehicleProfile) {
            $currentKm = $asset->vehicleProfile->current_odometer_km;
            $record['odometer_km_at_service'] = $currentKm - rand(1000, 5000);
            $record['next_service_target_odometer_km'] = $currentKm + rand(5000, 10000);
            $record['next_service_date'] = Carbon::now()->addMonths(rand(1, 6));
            $record['service_tasks'] = [
                ['task' => 'Ganti oli mesin', 'completed' => true],
                ['task' => 'Cek tekanan ban', 'completed' => true],
            ];

            if ($record['type'] === MaintenanceType::PREVENTIVE && $status === MaintenanceStatus::COMPLETED) {
                $record['next_service_date_before'] = Carbon::now()->subMonths(rand(1, 3));
            }
        }

        AssetMaintenance::create($record);
    }

    private function createInsurancePolicy(Asset $asset, $insurances): void
    {
        if ($insurances->isEmpty()) return;

        $insurance = $insurances->random();
        $startDate = Carbon::now()->subMonths(rand(1, 12));
        $endDate = $startDate->copy()->addMonths(rand(6, 18));
        $isActive = rand(0, 100) < 60;

        InsurancePolicy::create([
            'id' => Str::uuid()->toString(),
            'asset_id' => $asset->id,
            'insurance_id' => $insurance->id,
            'policy_no' => 'POL-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'policy_type' => collect(InsurancePolicyType::cases())->random()->value,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $isActive ? InsuranceStatus::ACTIVE : InsuranceStatus::INACTIVE,
            'notes' => 'Insurance policy via seeder',
        ]);
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

    private function randomPriority(): MaintenancePriority
    {
        $weights = [3, 5, 2];
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;
        $priorities = [MaintenancePriority::LOW, MaintenancePriority::MEDIUM, MaintenancePriority::HIGH];

        foreach ($weights as $index => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $priorities[$index];
            }
        }

        return MaintenancePriority::MEDIUM;
    }
}
