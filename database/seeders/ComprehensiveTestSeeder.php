<?php

namespace Database\Seeders;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\AssetMaintenance;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComprehensiveTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates test data for:
     * - Active loans (normal & overdue)
     * - Vehicles with maintenance overdue scenarios
     *   (overdue date, overdue km, both, soon warning, normal)
     */
    public function run(): void
    {
        // Validasi dependencies
        $companies = Company::all();
        $categories = Category::all();
        $branches = Branch::all();
        $employees = Employee::all();

        if ($companies->isEmpty() || $categories->isEmpty() || $branches->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('Required seeders (Company, Category, Branch, Employee) must run first.');

            return;
        }

        $vehicleCategory = $categories->where('name', 'Kendaraan')->first();
        $computerCategory = $categories->where('name', 'Komputer & Laptop')->first();
        $officeCategory = $categories->where('name', 'Peralatan Kantor')->first();

        if (! $vehicleCategory || ! $computerCategory || ! $officeCategory) {
            $this->command->warn('Required categories not found.');

            return;
        }

        $branchList = $branches->all();
        $branchCount = count($branchList);
        $this->command->info("Distributing test data across {$branchCount} branches: ".implode(', ', array_map(fn ($b) => $b->name, $branchList)));

        $scenarios = [
            'loan' => [
                'method' => 'createActiveLoans',
                'count' => 3, // 2 normal, 1 overdue
            ],
            'overdue_date' => [
                'method' => 'createOverdueDateVehicles',
                'count' => 2,
            ],
            'overdue_km' => [
                'method' => 'createOverdueKmVehicles',
                'count' => 2,
            ],
            'overdue_both' => [
                'method' => 'createBothOverdueVehicles',
                'count' => 1,
            ],
            'soon_warning' => [
                'method' => 'createSoonWarningVehicles',
                'count' => 2,
            ],
            'normal' => [
                'method' => 'createNormalVehicles',
                'count' => 2,
            ],
        ];

        foreach ($branchList as $branch) {
            $company = $branch->company_id ? $companies->firstWhere('id', $branch->company_id) : $companies->first();
            if (! $company) {
                $company = $companies->first();
            }

            $this->command->info("--- Branch: {$branch->name} ---");

            foreach ($scenarios as $key => $scenario) {
                $method = $scenario['method'];
                $this->command->info("  → Creating scenario [{$key}]...");
                $this->{$method}($company, $branch, $vehicleCategory, $categories, $employees);
            }
        }

        $this->command->info('Comprehensive test data created successfully across all branches!');
    }

    /**
     * Create active loans - mix of normal and overdue.
     */
    private function createActiveLoans($company, $branch, $vehicleCategory, $categories, $employees): void
    {
        $computerCategory = $categories->where('name', 'Komputer & Laptop')->first();
        $officeCategory = $categories->where('name', 'Peralatan Kantor')->first();

        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';

        $loanAssets = [
            [
                'name' => "Laptop ASUS [TEST-LOAN-NORMAL] {$branchTag}",
                'category_id' => $computerCategory->id,
                'value' => 12000000,
                'days_until_due' => 14,
            ],
            [
                'name' => "Laptop Lenovo [TEST-LOAN-NORMAL] {$branchTag}",
                'category_id' => $computerCategory->id,
                'value' => 10000000,
                'days_until_due' => 7,
            ],
            [
                'name' => "Printer Canon [TEST-LOAN-OVERDUE] {$branchTag}",
                'category_id' => $officeCategory->id,
                'value' => 4500000,
                'days_until_due' => -10,
            ],
        ];

        foreach ($loanAssets as $loanData) {
            $asset = Asset::create([
                'id' => Str::uuid(),
                'code' => generate_asset_code($loanData['category_id'], $branch->id),
                'tag_code' => generate_asset_tag_code(),
                'name' => $loanData['name'],
                'category_id' => $loanData['category_id'],
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'status' => AssetStatus::ON_LOAN,
                'condition' => AssetCondition::GOOD,
                'value' => $loanData['value'],
                'purchase_date' => '2023-01-15',
                'description' => "Test asset for loan scenario - {$branch->name}",
            ]);

            $branchEmployees = $employees->where('branch_id', $branch->id);
            $employee = $branchEmployees->isNotEmpty() ? $branchEmployees->random() : $employees->random();

            $checkoutAt = Carbon::now()->subDays(rand(5, 20));
            $dueAt = Carbon::now()->addDays($loanData['days_until_due']);

            AssetLoan::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'checkout_at' => $checkoutAt,
                'due_at' => $dueAt,
                'checkin_at' => null,
                'condition_out' => AssetCondition::GOOD,
                'condition_in' => null,
                'notes' => "Test loan - {$branch->name} - ".($loanData['days_until_due'] < 0 ? 'OVERDUE' : 'NORMAL'),
            ]);
        }
    }

    /**
     * Create vehicles with overdue date (next_service_date in the past).
     */
    private function createOverdueDateVehicles($company, $branch, $category, $vehicleCategory, $employees): void
    {
        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';
        $vehicles = [
            [
                'name' => "Toyota Avanza [TEST-OVERDUE-DATE] {$branchTag}",
                'plate' => 'B 1001 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 10,
                'overdue_km' => false,
            ],
            [
                'name' => "Honda Civic [TEST-OVERDUE-DATE] {$branchTag}",
                'plate' => 'B 1002 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 30,
                'overdue_km' => false,
            ],
        ];

        $this->createVehiclesWithSchedule($vehicles, $company, $branch, $category, $employees);
    }

    /**
     * Create vehicles with overdue km (current_odometer_km >= service_target_odometer_km).
     */
    private function createOverdueKmVehicles($company, $branch, $category, $vehicleCategory, $employees): void
    {
        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';
        $vehicles = [
            [
                'name' => "Suzuki Carry [TEST-OVERDUE-KM] {$branchTag}",
                'plate' => 'B 2001 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 0,
                'overdue_km' => 2000,
                'current_km' => 52000,
                'target_km' => 50000,
            ],
            [
                'name' => "Mitsubishi L300 [TEST-OVERDUE-KM] {$branchTag}",
                'plate' => 'B 2002 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 0,
                'overdue_km' => 5000,
                'current_km' => 100000,
                'target_km' => 95000,
            ],
        ];

        $this->createVehiclesWithSchedule($vehicles, $company, $branch, $category, $employees);
    }

    /**
     * Create vehicles with both date and km overdue.
     */
    private function createBothOverdueVehicles($company, $branch, $category, $vehicleCategory, $employees): void
    {
        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';
        $vehicles = [
            [
                'name' => "Daihatsu Xenia [TEST-OVERDUE-BOTH] {$branchTag}",
                'plate' => 'B 3001 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 5,
                'overdue_km' => 5000,
                'current_km' => 65000,
                'target_km' => 60000,
            ],
        ];

        $this->createVehiclesWithSchedule($vehicles, $company, $branch, $category, $employees);
    }

    /**
     * Create vehicles with "soon" warning (within 7 days or 1000 km).
     */
    private function createSoonWarningVehicles($company, $branch, $category, $vehicleCategory, $employees): void
    {
        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';
        $vehicles = [
            [
                'name' => "Toyota Innova [TEST-SOON-DATE] {$branchTag}",
                'plate' => 'B 4001 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => -5,
                'overdue_km' => false,
            ],
            [
                'name' => "Wuling Air EV [TEST-SOON-KM] {$branchTag}",
                'plate' => 'B 4002 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 0,
                'overdue_km' => -800,
                'current_km' => 49200,
                'target_km' => 50000,
            ],
        ];

        $this->createVehiclesWithSchedule($vehicles, $company, $branch, $category, $employees);
    }

    /**
     * Create vehicles with normal maintenance schedule.
     */
    private function createNormalVehicles($company, $branch, $category, $vehicleCategory, $employees): void
    {
        $branchTag = '[BRANCH:'.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 6)).']';
        $vehicles = [
            [
                'name' => "Toyota Fortuner [TEST-NORMAL] {$branchTag}",
                'plate' => 'B 5001 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => -60,
                'overdue_km' => false,
            ],
            [
                'name' => "Toyota Calya [TEST-NORMAL] {$branchTag}",
                'plate' => 'B 5002 '.strtoupper(substr(str_replace(' ', '', $branch->name), 0, 4)),
                'days_overdue' => 0,
                'overdue_km' => -5000,
                'current_km' => 45000,
                'target_km' => 50000,
            ],
        ];

        $this->createVehiclesWithSchedule($vehicles, $company, $branch, $category, $employees);
    }

    /**
     * Helper: Create vehicles with custom maintenance schedule.
     */
    private function createVehiclesWithSchedule(array $vehicles, $company, $branch, $category, $employees): void
    {
        foreach ($vehicles as $v) {
            $asset = Asset::create([
                'id' => Str::uuid(),
                'code' => generate_asset_code($category->id, $branch->id),
                'tag_code' => generate_asset_tag_code(),
                'name' => $v['name'],
                'category_id' => $category->id,
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'status' => AssetStatus::ACTIVE,
                'condition' => AssetCondition::GOOD,
                'value' => 200000000,
                'purchase_date' => '2022-01-15',
                'description' => 'Test vehicle for maintenance overdue scenario',
            ]);

            // Determine dates and km
            $nextServiceDate = $v['days_overdue'] > 0
                ? Carbon::now()->subDays($v['days_overdue'])
                : ($v['days_overdue'] < 0 ? Carbon::now()->addDays(abs($v['days_overdue'])) : null);

            $currentKm = $v['current_km'] ?? ($v['overdue_km'] !== false ? ($v['overdue_km'] < 0 ? 50000 + $v['overdue_km'] : 45000) : 45000);
            $targetKm = $v['target_km'] ?? ($v['overdue_km'] !== false ? 50000 : 50000);

            $lastServiceDate = $nextServiceDate
                ? $nextServiceDate->copy()->subMonths(6)
                : Carbon::now()->subMonths(3);

            VehicleProfile::create([
                'asset_id' => $asset->id,
                'year_purchase' => 2022,
                'year_manufacture' => 2022,
                'current_odometer_km' => $currentKm,
                'last_service_date' => $lastServiceDate,
                'service_target_odometer_km' => $targetKm,
                'next_service_date' => $nextServiceDate,
                'plate_no' => $v['plate'],
                'vin' => 'TESTVIN'.str_pad((string) rand(1, 999999), 9, '0', STR_PAD_LEFT),
            ]);

            // Create a completed maintenance record to simulate the history
            $employee = $employees->random();
            $serviceOdometer = $currentKm - rand(1000, 3000);

            AssetMaintenance::create([
                'asset_id' => $asset->id,
                'employee_id' => $employee->id,
                'title' => 'Service Rutin',
                'type' => MaintenanceType::PREVENTIVE,
                'status' => MaintenanceStatus::COMPLETED,
                'priority' => MaintenancePriority::MEDIUM,
                'started_at' => $lastServiceDate,
                'estimated_completed_at' => $lastServiceDate->copy()->addDays(2),
                'completed_at' => $lastServiceDate->copy()->addDays(2),
                'cost' => rand(500000, 1500000),
                'technician_name' => 'Teknisi Test',
                'vendor_name' => 'Bengkel Test',
                'notes' => 'Test maintenance record - '.Str::random(8),
                'odometer_km_at_service' => $serviceOdometer,
                'next_service_target_odometer_km' => $targetKm,
                'next_service_date' => $nextServiceDate,
                'next_service_date_before' => $lastServiceDate->copy()->subMonths(6),
                'invoice_no' => 'INV-TEST-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'service_tasks' => [
                    ['task' => 'Ganti oli mesin', 'completed' => true],
                    ['task' => 'Cek tekanan ban', 'completed' => true],
                ],
            ]);

            // Create odometer log
            VehicleOdometerLog::create([
                'asset_id' => $asset->id,
                'odometer_km' => $currentKm,
                'read_at' => Carbon::now()->subDays(rand(1, 30)),
                'source' => 'manual',
                'notes' => 'Test odometer log',
            ]);
        }
    }
}
