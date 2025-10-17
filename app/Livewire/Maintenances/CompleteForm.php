<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Models\AssetMaintenance;
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

    public $next_service_target_odometer_km = '';

    public $next_service_date = '';

    public $notes = '';

    // Helper properties
    public $asset;

    public array $service_tasks = [];

    public array $service_details = [];

    public string $branchId;

    protected function rules()
    {
        $rules = [
            'cost' => 'required|numeric|min:0',
            'invoice_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'next_service_date' => 'nullable|date',
            'service_tasks' => 'nullable|array',
            'service_tasks.*.task' => 'nullable|string|max:255',
            'service_tasks.*.completed' => 'nullable|boolean',
            'service_details' => 'nullable|array',
            'service_details.*.name' => 'nullable|string|max:255',
            'service_details.*.qty' => 'nullable|integer|min:0',
        ];

        // Dynamic validation for odometer fields based on current vehicle odometer
        if ($this->asset && $this->asset->vehicleProfile) {
            $minOdometer = $this->asset->vehicleProfile->current_odometer_km ?? 0;
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
            'next_service_target_odometer_km.integer' => 'Target odometer service berikutnya harus berupa angka.',
            'service_tasks.*.task.max' => 'Nama tugas layanan maksimal 255 karakter.',
            'service_tasks.*.completed.boolean' => 'Centang tugas hanya bernilai ya/tidak.',
            'service_details.*.qty.integer' => 'Jumlah item layanan harus berupa angka.',
            'service_details.*.qty.min' => 'Jumlah item layanan tidak boleh negatif.',
            'service_details.*.name.max' => 'Nama layanan maksimal 255 karakter.',
        ];

        // Dynamic messages for odometer validation based on current vehicle odometer
        if ($this->asset && $this->asset->vehicleProfile && $this->asset->vehicleProfile->current_odometer_km) {
            $currentOdometer = $this->asset->vehicleProfile->current_odometer_km;
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
    }

    public function loadMaintenance()
    {
        $maintenance = AssetMaintenance::with(['asset.vehicleProfile', 'asset.category'])->findOrFail($this->maintenanceId);

        $this->asset = $maintenance->asset;
        $this->cost = $maintenance->cost;
        $this->notes = $maintenance->notes;
        $this->next_service_target_odometer_km = $maintenance->next_service_target_odometer_km;
        $this->next_service_date = $maintenance->next_service_date?->format('Y-m-d');
        $this->invoice_no = $maintenance->invoice_no;
        $this->service_details = $maintenance->service_details ?? [];
        $this->service_tasks = $maintenance->service_tasks ?? [];
    }

    public function save()
    {
        try {
            $this->validate();
            $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);

            $data = [
                'status' => MaintenanceStatus::COMPLETED,
                'completed_at' => now(),
                'cost' => $this->cost ?: 0,
                'notes' => $this->notes,
                'invoice_no' => $this->invoice_no ?: null,
                'service_tasks' => $this->service_tasks ?: [],
                'service_details' => $this->service_details ?: [],
            ];

            // Add odometer fields for vehicles
            if ($this->isVehicle) {
                $data['next_service_target_odometer_km'] = $this->next_service_target_odometer_km ?: null;
            }

            // Add next service date
            $data['next_service_date'] = $this->next_service_date ?: null;

            $maintenance->update($data);
            $this->dispatch('close-completed-drawer');

            $this->success('Perawatan berhasil diselesaikan!');

            $this->dispatch('reload-page');

        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function getIsVehicleProperty()
    {
        return $this->asset && $this->asset->category->name === 'Kendaraan';
    }

    public function getCanCompleteProperty()
    {
        if (! $this->maintenanceId) {
            return false;
        }

        $maintenance = AssetMaintenance::find($this->maintenanceId);

        return $maintenance && $maintenance->status === MaintenanceStatus::IN_PROGRESS;
    }

    public function addServiceDetail()
    {
        $this->service_details[] = ['name' => '', 'qty' => 1];
    }

    public function removeServiceDetail($index)
    {
        unset($this->service_details[$index]);
        $this->service_details = array_values($this->service_details);
    }

    public function render()
    {
        return view('livewire.maintenances.complete-form');
    }
}
