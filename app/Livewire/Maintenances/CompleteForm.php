<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Models\AssetMaintenance;
use App\Models\Employee;
use App\Support\SessionKey;
use Livewire\Component;
use Mary\Traits\Toast;

class CompleteForm extends Component
{
    use Toast;

    public $maintenanceId;

    // Form properties
    public $invoice_no = '';

    public $cost = '';

    public $odometer_km_at_service = '';

    public $next_service_target_odometer_km = '';

    public $next_service_date = '';

    public $notes = '';

    public $employee_id = '';

    public $completed_at = '';

    public $technician_name = '';

    public $vendor_name = '';

    // Helper properties
    public $isEdit = false;

    public $asset;

    public string $branchId;

    // Cache properties
    private $employeesCache = null;

    protected function rules()
    {
        $rules = [
            'cost' => 'required|numeric|min:0',
            'invoice_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'next_service_date' => 'nullable|date',
            'employee_id' => 'nullable|exists:employees,id',
            'completed_at' => 'nullable|date',
            'technician_name' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
        ];

        // Dynamic validation for odometer fields based on current vehicle odometer
        if ($this->asset && $this->asset->vehicleProfile) {
            $minOdometer = $this->asset->vehicleProfile->current_odometer_km ?? 0;
            $rules['odometer_km_at_service'] = "required|integer|min:{$minOdometer}";
            $rules['next_service_target_odometer_km'] = "nullable|integer|min:{$minOdometer}";
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [
            'cost.required' => 'Biaya wajib diisi.',
            'cost.numeric' => 'Biaya harus berupa angka.',
            'cost.min' => 'Biaya tidak boleh negatif.',
            'invoice_no.max' => 'Nomor invoice maksimal 255 karakter.',
            'next_service_date.date' => 'Tanggal service berikutnya harus berupa tanggal yang valid.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid.',
            'completed_at.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'technician_name.max' => 'Nama teknisi maksimal 255 karakter.',
            'vendor_name.max' => 'Nama vendor maksimal 255 karakter.',
            'odometer_km_at_service.required' => 'Odometer saat service wajib diisi untuk kendaraan.',
            'odometer_km_at_service.integer' => 'Odometer saat service harus berupa angka.',
            'next_service_target_odometer_km.integer' => 'Target odometer service berikutnya harus berupa angka.',
        ];

        // Dynamic messages for odometer validation based on current vehicle odometer
        if ($this->asset && $this->asset->vehicleProfile && $this->asset->vehicleProfile->current_odometer_km) {
            $currentOdometer = $this->asset->vehicleProfile->current_odometer_km;
            $messages['odometer_km_at_service.min'] = "Odometer saat service tidak boleh kurang dari odometer saat ini ({$currentOdometer} KM).";
            $messages['next_service_target_odometer_km.min'] = "Target odometer service berikutnya tidak boleh kurang dari odometer saat ini ({$currentOdometer} KM).";
        }

        return $messages;
    }

    public function mount($maintenanceId = null)
    {
        $this->branchId = session_get(SessionKey::BranchId);
        $this->maintenanceId = $maintenanceId;

        if ($maintenanceId) {
            $this->loadMaintenance();
        }

        // Set default completed_at to now
        $this->completed_at = now()->format('Y-m-d\TH:i');
    }

    public function loadMaintenance()
    {
        $maintenance = AssetMaintenance::with(['asset.vehicleProfile', 'employee'])->findOrFail($this->maintenanceId);

        $this->asset = $maintenance->asset;
        $this->employee_id = $maintenance->employee_id;
        $this->cost = $maintenance->cost;
        $this->technician_name = $maintenance->technician_name;
        $this->vendor_name = $maintenance->vendor_name;
        $this->notes = $maintenance->notes;
        $this->odometer_km_at_service = $maintenance->odometer_km_at_service;
        $this->next_service_target_odometer_km = $maintenance->next_service_target_odometer_km;
        $this->next_service_date = $maintenance->next_service_date?->format('Y-m-d');
        $this->invoice_no = $maintenance->invoice_no;
        $this->completed_at = $maintenance->completed_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');

        // Set default odometer if vehicle and not set
        if ($this->isVehicle && ! $this->odometer_km_at_service && $this->asset->vehicleProfile) {
            $this->odometer_km_at_service = $this->asset->vehicleProfile->current_odometer_km;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);

            $data = [
                'status' => MaintenanceStatus::COMPLETED,
                'completed_at' => $this->completed_at,
                'cost' => $this->cost ?: 0,
                'technician_name' => $this->technician_name ?: null,
                'vendor_name' => $this->vendor_name ?: null,
                'notes' => $this->notes,
                'invoice_no' => $this->invoice_no ?: null,
                'employee_id' => $this->employee_id ?: null,
            ];

            // Add odometer fields for vehicles
            if ($this->isVehicle) {
                $data['odometer_km_at_service'] = $this->odometer_km_at_service ?: null;
                $data['next_service_target_odometer_km'] = $this->next_service_target_odometer_km ?: null;
            }

            // Add next service date
            $data['next_service_date'] = $this->next_service_date ?: null;

            $maintenance->update($data);

            $this->success('Perawatan berhasil diselesaikan!');

            $this->dispatch('refresh-kanban');
            $this->dispatch('close-completed-drawer');
            $this->dispatch('reload-page');

        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function getIsVehicleProperty()
    {
        return $this->asset && $this->asset->vehicleProfile !== null;
    }

    public function getEmployeesProperty()
    {
        if ($this->employeesCache === null) {
            $this->employeesCache = Employee::where('branch_id', $this->branchId)
                ->orderBy('name')
                ->get()
                ->map(function ($employee) {
                    return (object) [
                        'value' => $employee->id,
                        'label' => $employee->name.' - '.$employee->position,
                    ];
                });
        }

        return $this->employeesCache;
    }

    public function render()
    {
        return view('livewire.maintenances.complete-form', [
            'employees' => $this->employees,
        ]);
    }
}
