<?php

namespace App\Livewire\Maintenances;

use App\Enums\AssetStatus;
use App\Enums\InsuranceClaimSource;
use App\Enums\InsuranceClaimStatus;
use App\Enums\InsuranceStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\User;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
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

    public array $assets = [];

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

    public $showVehicleProfileConfirmation = false;

    public $selectedAssetForProfile = null;

    public $isEdit = false;

    public string $branchId = '';

    public bool $is_asurance_active = false;

    public bool $hasActiveInsurancePolicy = false;

    public bool $showInsuranceClaimPrompt = false;

    public ?string $pendingClaimId = null;

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
        $this->branchId = session_get(SessionKey::BranchId) ?? '';
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
            // Prefill combobox options with default employees list
            $this->dispatch('combobox-set-employees', $this->employees);
        }
        $this->loadAssets();
        $this->refreshInsurancePolicyFlag();

    }

    private function loadInitialEmployees()
    {
        $this->employees = Employee::where('is_active', true)
            ->where('branch_id', $this->branchId)
            ->limit(10)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'email'])
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'full_name' => $employee->full_name,
                    'email' => $employee->email,
                ];
            })->toArray();
    }

    public function loadMaintenance()
    {

        // code...
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

        // Set checkbox based on existing insurance claim linked to this maintenance
        $this->is_asurance_active = InsuranceClaim::query()
            ->where('asset_maintenance_id', $maintenance->id)
            ->exists();

        $this->refreshInsurancePolicyFlag($this->asset_id);
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
                'email' => $selectedEmployee->email,
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
                'service_tasks' => array_values(array_filter($this->service_tasks, function ($task) {
                    return ! empty(trim($task));
                })),
            ];

            if ($this->isEdit) {
                $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);
                $maintenance->update($data);
                $this->createInsuranceClaimIfNeeded($maintenance);
                $this->success('Perawatan berhasil diperbarui!');
            } else {
                $maintenance = AssetMaintenance::create($data);
                $this->createInsuranceClaimIfNeeded($maintenance);
                $this->success('Perawatan berhasil ditambahkan!');
            }

            // If insurance claim checkbox is active, prompt user to open claim now or later
            if ($this->is_asurance_active) {
                $claimId = InsuranceClaim::query()
                    ->where('asset_maintenance_id', $maintenance->id)
                    ->value('id');

                if ($claimId) {
                    $this->pendingClaimId = (string) $claimId;
                    // Fire browser event to open modal at page level
                    $this->dispatch('open-insurance-claim-modal', ['claim_id' => $claimId]);

                    // Stop here; wait for user's choice via modal
                    return;
                }
            }

            // Default behavior: refresh kanban, close drawer, and reload page
            $this->dispatch('refresh-kanban');
            $this->dispatch('close-drawer');
            $this->dispatch('reload-page');

            // Default post-save behavior
            $this->dispatch('refresh-kanban');
            $this->dispatch('close-drawer');
            $this->dispatch('reload-page');
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    private function refreshInsurancePolicyFlag($assetId = null)
    {
        $assetId = $assetId ?? $this->asset_id;
        if (! $assetId) {
            $this->hasActiveInsurancePolicy = false;
            $this->is_asurance_active = false;

            return;
        }
        $policyId = $this->getLatestActivePolicyIdForAsset($assetId);
        $this->hasActiveInsurancePolicy = ! empty($policyId);
        if (! $this->hasActiveInsurancePolicy) {
            $this->is_asurance_active = false;
        }
    }

    private function getLatestActivePolicyIdForAsset($assetId)
    {
        if (! $assetId) {
            return null;
        }

        return InsurancePolicy::query()
            ->where('asset_id', $assetId)
            ->where('status', InsuranceStatus::ACTIVE->value)
            ->orderByDesc('start_date')
            ->value('id');
    }

    private function createInsuranceClaimIfNeeded(AssetMaintenance $maintenance): void
    {
        if (! $this->is_asurance_active) {
            return;
        }

        $policyId = $this->getLatestActivePolicyIdForAsset($this->asset_id);
        if (! $policyId) {
            return;
        }

        InsuranceClaim::create([
            'policy_id' => $policyId,
            'asset_id' => $this->asset_id,
            'source' => InsuranceClaimSource::MAINTENANCE->value,
            'asset_maintenance_id' => $maintenance->id,
            'status' => InsuranceClaimStatus::DRAFT->value,
            'created_by' => Auth::id(),
        ]);
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
        $this->is_asurance_active = false;
        $this->hasActiveInsurancePolicy = false;
        $this->showInsuranceClaimPrompt = false;
        $this->pendingClaimId = null;
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

    #[On('combobox-load-assets')]
    public function loadAssets($search = '')
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        $query = Asset::select(['id', 'name', 'code', 'tag_code', 'image'])
            ->when($currentBranchId, fn ($q) => $q->where('branch_id', $currentBranchId))
            ->orderBy('name');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('tag_code', 'like', "%{$search}%");
            });
        }

        $this->assets = $query->take(20)->get(['id', 'name', 'code', 'tag_code', 'image'])->toArray();

        $this->dispatch('combobox-set-assets', $this->assets);
    }

    #[On('combobox-load-employees')]
    public function loadEmployees($search = '')
    {
        $branchId = session_get(SessionKey::BranchId);
        $query = Employee::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true);

        if (! empty($search)) {
            $query->where('full_name', 'like', "%$search%");
        }

        $this->employees = $query->orderBy('full_name')
            ->get(['id', 'full_name', 'email'])
            ->toArray();

        // Kirim data options terbaru ke combobox instance bernama 'employees'
        $this->dispatch('combobox-set-employees', $this->employees);
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

    public function updatedAssetId($value)
    {
        if ($value && $this->getIsVehicleForAsset($value)) {
            $this->checkVehicleProfile($value);
        }
        $this->refreshInsurancePolicyFlag($value);
    }

    private function getIsVehicleForAsset($assetId)
    {
        $asset = Asset::with('category')->find($assetId);
        if (! $asset || ! $asset->category) {
            return false;
        }

        return $asset->category->name === 'Kendaraan';
    }

    private function checkVehicleProfile($assetId)
    {
        $asset = Asset::with(['category', 'vehicleProfile'])->find($assetId);

        if ($asset && $asset->category && $asset->category->name === 'Kendaraan') {
            if (! $asset->vehicleProfile) {
                // Show warning and ask for confirmation
                $this->warning(
                    'Kendaraan yang dipilih belum memiliki profil kendaraan. Profil kendaraan diperlukan untuk membuat maintenance record.',
                    'Profil Kendaraan Diperlukan'
                );

                // Reset asset selection
                $this->asset_id = '';

                // Set a flag to show confirmation buttons
                $this->showVehicleProfileConfirmation = true;
                $this->selectedAssetForProfile = $assetId;
            }
        }
    }

    public function confirmCreateVehicleProfile()
    {
        if ($this->selectedAssetForProfile) {
            $this->redirectRoute('vehicles.index', [
                'action' => 'save-profile',
                'asset_id' => $this->selectedAssetForProfile,
            ]);
        }
    }

    public function cancelVehicleProfileCreation()
    {
        $this->showVehicleProfileConfirmation = false;
        $this->selectedAssetForProfile = null;
        $this->info('Pemilihan kendaraan dibatalkan. Silakan pilih asset lain.');
    }

    public function getAssetProperty()
    {
        if (! $this->asset_id) {
            return null;
        }

        return Asset::with(['category', 'vehicleProfile'])->find($this->asset_id);
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

    #[On('confirm-open-claim')]
    public function confirmOpenClaim()
    {
        if ($this->pendingClaimId) {
            $claimId = $this->pendingClaimId;
            $this->pendingClaimId = null;

            return $this->redirectRoute('insurance-claims.index', [
                'action' => 'edit',
                'claim_id' => $claimId,
            ]);
        }

        // If there's no pending claim id, continue with default behavior
        $this->dismissClaimPrompt();
    }

    #[On('dismiss-insurance-claim-prompt')]
    public function dismissClaimPrompt()
    {
        $this->pendingClaimId = null;
        // Continue with default post-save behavior
        $this->dispatch('refresh-kanban');
        $this->dispatch('close-drawer');
        $this->dispatch('reload-page');
    }
}
