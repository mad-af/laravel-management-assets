<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $maintenanceId;

    public $asset_id = '';

    public $title = '';

    public $type = '';

    public $status = '';

    public $priority = '';

    public $started_at = '';

    public $estimated_completed_at = '';

    public $completed_at = '';

    public $cost = '';

    public $technician_name = '';

    public $vendor_name = '';

    public $notes = '';

    public $odometer_km_at_service = '';

    public $next_service_target_odometer_km = '';

    public $next_service_date = '';

    public $invoice_no = '';

    public $isEdit = false;

    protected $rules = [
        'asset_id' => 'required|exists:assets,id',
        'title' => 'required|string|max:255',
        'type' => 'required',
        'status' => 'required',
        'priority' => 'required',
        'started_at' => 'nullable|date',
        'estimated_completed_at' => 'nullable|date',
        'completed_at' => 'nullable|date',
        'cost' => 'nullable|numeric|min:0',
        'technician_name' => 'nullable|string|max:255',
        'vendor_name' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'odometer_km_at_service' => 'nullable|integer|min:0',
        'next_service_target_odometer_km' => 'nullable|integer|min:0',
        'next_service_date' => 'nullable|date',
        'invoice_no' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'asset_id.required' => 'Aset wajib dipilih.',
        'asset_id.exists' => 'Aset yang dipilih tidak valid.',
        'title.required' => 'Judul wajib diisi.',
        'title.max' => 'Judul maksimal 255 karakter.',
        'type.required' => 'Jenis perawatan wajib dipilih.',
        'status.required' => 'Status wajib dipilih.',
        'priority.required' => 'Prioritas wajib dipilih.',
        'cost.numeric' => 'Biaya harus berupa angka.',
        'cost.min' => 'Biaya tidak boleh negatif.',
        'started_at.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
        'estimated_completed_at.date' => 'Tanggal estimasi selesai harus berupa tanggal yang valid.',
        'completed_at.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
        'next_service_date.date' => 'Tanggal service berikutnya harus berupa tanggal yang valid.',
        'odometer_km_at_service.integer' => 'Odometer saat service harus berupa angka.',
        'odometer_km_at_service.min' => 'Odometer saat service tidak boleh negatif.',
        'next_service_target_odometer_km.integer' => 'Target odometer service berikutnya harus berupa angka.',
        'next_service_target_odometer_km.min' => 'Target odometer service berikutnya tidak boleh negatif.',
    ];

    protected $listeners = [
        'editMaintenance' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($maintenanceId = null)
    {
        $this->maintenanceId = $maintenanceId;

        if ($maintenanceId) {
            $this->isEdit = true;
            $this->loadMaintenance();
        } else {
            // Set default values for new maintenance
            $this->status = MaintenanceStatus::OPEN->value;
            $this->priority = MaintenancePriority::MEDIUM->value;
        }
    }

    public function loadMaintenance()
    {
        $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);

        $this->asset_id = $maintenance->asset_id;
        $this->title = $maintenance->title;
        $this->type = $maintenance->type->value;
        $this->status = $maintenance->status->value;
        $this->priority = $maintenance->priority->value;
        $this->started_at = $maintenance->started_at?->format('Y-m-d\TH:i');
        $this->estimated_completed_at = $maintenance->estimated_completed_at?->format('Y-m-d\TH:i');
        $this->completed_at = $maintenance->completed_at?->format('Y-m-d\TH:i');
        $this->cost = $maintenance->cost;
        $this->technician_name = $maintenance->technician_name;
        $this->vendor_name = $maintenance->vendor_name;
        $this->notes = $maintenance->notes;
        $this->odometer_km_at_service = $maintenance->odometer_km_at_service;
        $this->next_service_target_odometer_km = $maintenance->next_service_target_odometer_km;
        $this->next_service_date = $maintenance->next_service_date?->format('Y-m-d');
        $this->invoice_no = $maintenance->invoice_no;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'title' => $this->title,
                'type' => $this->type,
                'status' => $this->status,
                'priority' => $this->priority,
                'started_at' => $this->started_at ?: null,
                'estimated_completed_at' => $this->estimated_completed_at ?: null,
                'completed_at' => $this->completed_at ?: null,
                'cost' => $this->cost ?: 0,
                'technician_name' => $this->technician_name ?: null,
                'vendor_name' => $this->vendor_name ?: null,
                'notes' => $this->notes,
                'odometer_km_at_service' => $this->odometer_km_at_service ?: null,
                'next_service_target_odometer_km' => $this->next_service_target_odometer_km ?: null,
                'next_service_date' => $this->next_service_date ?: null,
                'invoice_no' => $this->invoice_no ?: null,
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
        $this->title = '';
        $this->type = '';
        $this->status = MaintenanceStatus::OPEN->value;
        $this->priority = MaintenancePriority::MEDIUM->value;
        $this->started_at = '';
        $this->estimated_completed_at = '';
        $this->completed_at = '';
        $this->cost = '';
        $this->technician_name = '';
        $this->vendor_name = '';
        $this->notes = '';
        $this->odometer_km_at_service = '';
        $this->next_service_target_odometer_km = '';
        $this->next_service_date = '';
        $this->invoice_no = '';
        $this->isEdit = false;
        $this->maintenanceId = null;
        $this->resetValidation();
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

    public function getAssetProperty()
    {
        if (! $this->asset_id) {
            return null;
        }

        return Asset::with(['category', 'vehicleProfile'])->find($this->asset_id);
    }

    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);
        $assets = Asset::with('category')
            ->where('branch_id', $currentBranchId)
            ->where('status', '!=', \App\Enums\AssetStatus::LOST)
            ->orderBy('name')
            ->get()
            ->map(function ($asset) {
                return (object) [
                    'value' => $asset->id,
                    'label' => $asset->name.' ('.$asset->code.') - '.($asset->category->name ?? 'Tanpa Kategori'),
                ];
            });

        $maintenanceTypes = collect(MaintenanceType::cases())->map(function ($type) {
            return (object) [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        });

        $maintenanceStatuses = collect(MaintenanceStatus::cases())->map(function ($status) {
            return (object) [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        });

        $maintenancePriorities = collect(MaintenancePriority::cases())->map(function ($priority) {
            return (object) [
                'value' => $priority->value,
                'label' => $priority->label(),
            ];
        });

        $users = User::orderBy('name')->get()->map(function ($user) {
            return (object) [
                'value' => $user->id,
                'label' => $user->name.' ('.$user->role->value.')',
            ];
        });

        return view('livewire.maintenances.form', compact(
            'assets',
            'maintenanceTypes',
            'maintenanceStatuses',
            'maintenancePriorities',
            'users'
        ));
    }
}
