<?php

namespace App\Livewire\Maintenances;

use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use App\Models\User;
use App\Support\SessionKey;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $maintenanceId;

    public $asset_id = '';

    public $employee_id = '';

    public $employeeSearchTerm = '';

    public array $employees = [];

    public $title = '';

    public $type = '';

    public $status = '';

    public $priority = '';

    public $started_at = '';

    public $estimated_completed_at = '';

    public $vendor_name = '';

    public $odometer_km_at_service = '';

    public $notes = '';

    public array $service_tasks = [];

    public $isEdit = false;

    public string $branchId;

    // Cache properties to avoid repeated data fetching
    private $assetsCache = null;

    private $usersCache = null;

    private $maintenanceTypesCache = null;

    private $maintenanceStatusesCache = null;

    private $maintenancePrioritiesCache = null;

    protected function rules()
    {
        $rules = [
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'type' => 'required',
            'status' => 'required',
            'priority' => 'required',
            'started_at' => 'nullable|date',
            'estimated_completed_at' => 'nullable|date',
            'vendor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        // Dynamic validation for odometer fields based on current vehicle odometer
        if ($this->isVehicle) {
            $asset = Asset::with('vehicleProfile')->find($this->asset_id);
            if ($asset && $asset->vehicleProfile && $asset->vehicleProfile->current_odometer_km) {
                $minOdometer = $asset->vehicleProfile->current_odometer_km;
                $rules['odometer_km_at_service'] = "required|integer|min:{$minOdometer}";
            } else {
                $rules['odometer_km_at_service'] = 'required|integer|min:0';
            }
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [
            'asset_id.required' => 'Aset wajib dipilih.',
            'asset_id.exists' => 'Aset yang dipilih tidak valid.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'type.required' => 'Jenis perawatan wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'cost.numeric' => 'Biaya harus berupa angka.',
            'cost.min' => 'Biaya tidak boleh negatif.',
            'started_at.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'estimated_completed_at.date' => 'Tanggal estimasi selesai harus berupa tanggal yang valid.',
            'next_service_date.date' => 'Tanggal service berikutnya harus berupa tanggal yang valid.',
            'odometer_km_at_service.integer' => 'Odometer saat service harus berupa angka.',
            'next_service_target_odometer_km.integer' => 'Target odometer service berikutnya harus berupa angka.',
        ];

        // Dynamic messages for odometer validation based on current vehicle odometer
        if ($this->isVehicle) {
            $asset = Asset::with('vehicleProfile')->find($this->asset_id);
            $currentOdometer = $asset->vehicleProfile->current_odometer_km ?? 0;
            $messages['odometer_km_at_service.min'] = "Odometer saat service tidak boleh kurang dari odometer saat ini ({$currentOdometer} KM).";
        }

        return $messages;
    }

    protected $listeners = [
        'editMaintenance' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($maintenanceId = null)
    {
        $this->branchId = session_get(SessionKey::BranchId);
        $this->maintenanceId = $maintenanceId;

        // Initialize with empty employees array - will be loaded on demand
        $this->employees = [];

        if ($maintenanceId) {
            $this->isEdit = true;
            $this->loadMaintenance();
        } else {
            // Set default values for new maintenance
            $this->status = MaintenanceStatus::OPEN->value;
            $this->priority = MaintenancePriority::MEDIUM->value;
            $this->started_at = now()->format('Y-m-d');
            // Load initial employees for new form
            $this->loadInitialEmployees();
        }
    }

    private function loadInitialEmployees()
    {
        $this->employees = Employee::where('is_active', true)
            ->where('branch_id', $this->branchId)
            ->limit(10)
            ->orderBy('full_name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'full_name' => $employee->full_name,
                    'employee_number' => $employee->employee_number,
                ];
            })->toArray();
    }

    public function loadMaintenance()
    {
        $maintenance = AssetMaintenance::with('employee')->findOrFail($this->maintenanceId);

        $this->asset_id = $maintenance->asset_id;
        $this->employee_id = $maintenance->employee_id;
        $this->title = $maintenance->title;
        $this->type = $maintenance->type->value;
        $this->status = $maintenance->status->value;
        $this->priority = $maintenance->priority->value;
        $this->started_at = $maintenance->started_at?->format('Y-m-d');
        $this->estimated_completed_at = $maintenance->estimated_completed_at?->format('Y-m-d');
        $this->vendor_name = $maintenance->vendor_name;
        $this->notes = $maintenance->notes;
        $this->service_tasks = $maintenance->service_tasks ?? [];

        // Set default odometer if vehicle and not set
        if ($this->isVehicle && ! $this->odometer_km_at_service && $this->asset->vehicleProfile) {
            $this->odometer_km_at_service = $this->asset->vehicleProfile->current_odometer_km;
        }

        // Load employees with selected employee included
        $this->loadEmployeesWithSelected($maintenance->employee);
    }

    private function loadEmployeesWithSelected($selectedEmployee = null)
    {
        // Load initial employees
        $this->loadInitialEmployees();

        // Ensure selected employee is in search results for editing
        if ($selectedEmployee) {
            $selectedEmployeeData = [
                'id' => $selectedEmployee->id,
                'full_name' => $selectedEmployee->full_name,
                'employee_number' => $selectedEmployee->employee_number,
            ];

            // Check if selected employee is already in search results
            $exists = collect($this->employees)->contains('id', $selectedEmployee->id);

            if (! $exists) {
                // Add selected employee to the beginning of search results
                array_unshift($this->employees, $selectedEmployeeData);
            }
        }
    }

    public function save()
    {

        try {
            $this->validate();
            $data = [
                'asset_id' => $this->asset_id,
                'employee_id' => $this->employee_id ?: null,
                'title' => $this->title,
                'type' => $this->type,
                'status' => $this->status,
                'priority' => $this->priority,
                'started_at' => $this->started_at ?: now(),
                'estimated_completed_at' => $this->estimated_completed_at ?: null,
                'vendor_name' => $this->vendor_name ?: null,
                'odometer_km_at_service' => $this->odometer_km_at_service ?: null,
                'notes' => $this->notes,
                'service_tasks' => array_filter($this->service_tasks, function ($task) {
                    return ! empty(trim($task));
                }),
            ];

            if ($this->isEdit) {
                $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);
                $maintenance->update($data);
                $this->success('Perawatan berhasil diperbarui!');
            } else {
                AssetMaintenance::create($data);
                $this->success('Perawatan berhasil ditambahkan!');
            }

            $this->dispatch('refresh-kanban');
            $this->dispatch('close-drawer');
            $this->dispatch('reload-page');
        } catch (\Exception $e) {
            dd($e);
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->asset_id = '';
        $this->employee_id = '';
        $this->title = '';
        $this->type = '';
        $this->status = MaintenanceStatus::OPEN->value;
        $this->priority = MaintenancePriority::MEDIUM->value;
        $this->started_at = '';
        $this->estimated_completed_at = '';
        $this->vendor_name = '';
        $this->notes = '';
        $this->service_tasks = [];
        $this->isEdit = false;
        $this->maintenanceId = null;
        $this->resetValidation();

        // Reset cache
        $this->clearCache();

        // Reload initial employees
        $this->loadInitialEmployees();
    }

    private function clearCache()
    {
        $this->assetsCache = null;
        $this->usersCache = null;
        $this->maintenanceTypesCache = null;
        $this->maintenanceStatusesCache = null;
        $this->maintenancePrioritiesCache = null;
    }

    public function searchEmployees($value = '')
    {
        $this->employeeSearchTerm = $value;

        if (empty($value)) {
            // Return first 10 employees when no search term
            $this->employees = Employee::where('is_active', true)
                ->where('branch_id', $this->branchId)
                ->limit(10)
                ->orderBy('full_name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'full_name' => $employee->full_name,
                        'employee_number' => $employee->employee_number,
                    ];
                })->toArray();
        } else {
            // Search employees by name or employee number
            $this->employees = Employee::where('is_active', true)
                ->where('branch_id', $this->branchId)
                ->where(function ($query) use ($value) {
                    $query->where('full_name', 'like', '%'.$value.'%')
                        ->orWhere('employee_number', 'like', '%'.$value.'%');
                })
                ->limit(20)
                ->orderBy('full_name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'full_name' => $employee->full_name,
                        'employee_number' => $employee->employee_number,
                    ];
                })->toArray();
        }
    }

    // Computed properties to cache data and avoid repeated queries
    public function getAssetsProperty()
    {
        if ($this->assetsCache === null) {
            $this->assetsCache = Asset::with('category')
                ->where('branch_id', $this->branchId)
                // ->whereNotIn('status', [AssetStatus::MAINTENANCE, AssetStatus::LOST, AssetStatus::ON_LOAN])
                ->orderBy('name')
                ->get()
                ->map(function ($asset) {
                    return (object) [
                        'value' => $asset->id,
                        'label' => $asset->name.' ('.$asset->code.') - '.($asset->category->name ?? 'Tanpa Kategori'),
                    ];
                });
        }

        return $this->assetsCache;
    }

    public function getUsersProperty()
    {
        if ($this->usersCache === null) {
            $this->usersCache = User::orderBy('name')->get()->map(function ($user) {
                return (object) [
                    'value' => $user->id,
                    'label' => $user->name.' ('.$user->role->value.')',
                ];
            });
        }

        return $this->usersCache;
    }

    public function getMaintenanceTypesProperty()
    {
        if ($this->maintenanceTypesCache === null) {
            $this->maintenanceTypesCache = collect(MaintenanceType::cases())->map(function ($type) {
                return (object) [
                    'value' => $type->value,
                    'label' => $type->label(),
                ];
            });
        }

        return $this->maintenanceTypesCache;
    }

    public function getMaintenanceStatusesProperty()
    {
        if ($this->maintenanceStatusesCache === null) {
            $this->maintenanceStatusesCache = collect(MaintenanceStatus::cases())->map(function ($status) {
                return (object) [
                    'value' => $status->value,
                    'label' => $status->label(),
                ];
            });
        }

        return $this->maintenanceStatusesCache;
    }

    public function getMaintenancePrioritiesProperty()
    {
        if ($this->maintenancePrioritiesCache === null) {
            $this->maintenancePrioritiesCache = collect(MaintenancePriority::cases())->map(function ($priority) {
                return (object) [
                    'value' => $priority->value,
                    'label' => $priority->label(),
                ];
            });
        }

        return $this->maintenancePrioritiesCache;
    }

    public function getIsVehicleProperty()
    {
        if (! $this->asset_id) {
            return false;
        }

        $asset = Asset::with('category')->find($this->asset_id);
        if (! $asset || ! $asset->category) {
            return false;
        }

        return $asset->category->name === 'Kendaraan';
    }

    public function addServiceTask()
    {
        $this->service_tasks[] = '';
    }

    public function removeServiceTask($index)
    {
        unset($this->service_tasks[$index]);
        $this->service_tasks = array_values($this->service_tasks); // Re-index array
    }

    public function render()
    {
        return view('livewire.maintenances.form', [
            'assets' => $this->assets,
            'maintenanceTypes' => $this->maintenanceTypes,
            'maintenanceStatuses' => $this->maintenanceStatuses,
            'maintenancePriorities' => $this->maintenancePriorities,
            'users' => $this->users,
            'employees' => $this->employees,
        ]);
    }
}
