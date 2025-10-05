<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenancePriority;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $maintenanceId;
    public $asset_id = '';
    public $type = '';
    public $status = '';
    public $priority = '';
    public $title = '';
    public $description = '';
    public $cost = '';
    public $scheduled_date = '';
    public $completed_date = '';
    public $assigned_to = '';
    public $notes = '';
    public $isEdit = false;

    protected $rules = [
        'asset_id' => 'required|exists:assets,id',
        'type' => 'required',
        'status' => 'required',
        'priority' => 'required',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'cost' => 'nullable|numeric|min:0',
        'scheduled_date' => 'nullable|date',
        'completed_date' => 'nullable|date',
        'assigned_to' => 'nullable|exists:users,id',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'asset_id.required' => 'Aset wajib dipilih.',
        'asset_id.exists' => 'Aset yang dipilih tidak valid.',
        'type.required' => 'Jenis perawatan wajib dipilih.',
        'status.required' => 'Status wajib dipilih.',
        'priority.required' => 'Prioritas wajib dipilih.',
        'title.required' => 'Judul wajib diisi.',
        'title.max' => 'Judul maksimal 255 karakter.',
        'description.required' => 'Deskripsi wajib diisi.',
        'cost.numeric' => 'Biaya harus berupa angka.',
        'cost.min' => 'Biaya tidak boleh negatif.',
        'scheduled_date.date' => 'Tanggal terjadwal harus berupa tanggal yang valid.',
        'completed_date.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
        'assigned_to.exists' => 'Teknisi yang dipilih tidak valid.',
    ];

    protected $listeners = [
        'editMaintenance' => 'edit',
        'resetForm' => 'resetForm'
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
        $this->type = $maintenance->type->value;
        $this->status = $maintenance->status->value;
        $this->priority = $maintenance->priority->value;
        $this->title = $maintenance->title;
        $this->description = $maintenance->description;
        $this->cost = $maintenance->cost;
        $this->scheduled_date = $maintenance->scheduled_date?->format('Y-m-d');
        $this->completed_date = $maintenance->completed_date?->format('Y-m-d');
        $this->assigned_to = $maintenance->assigned_to;
        $this->notes = $maintenance->notes;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'type' => $this->type,
                'status' => $this->status,
                'priority' => $this->priority,
                'title' => $this->title,
                'description' => $this->description,
                'cost' => $this->cost ?: null,
                'scheduled_date' => $this->scheduled_date ?: null,
                'completed_date' => $this->completed_date ?: null,
                'assigned_to' => $this->assigned_to ?: null,
                'notes' => $this->notes,
            ];

            if ($this->isEdit) {
                $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);
                $maintenance->update($data);
                $this->success('Perawatan berhasil diperbarui!');
            } else {
                AssetMaintenance::create($data);
                $this->success('Perawatan berhasil ditambahkan!');
            }

            $this->dispatch('maintenance-saved');
            $this->dispatch('close-drawer');
            
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->asset_id = '';
        $this->type = '';
        $this->status = MaintenanceStatus::OPEN->value;
        $this->priority = MaintenancePriority::MEDIUM->value;
        $this->title = '';
        $this->description = '';
        $this->cost = '';
        $this->scheduled_date = '';
        $this->completed_date = '';
        $this->assigned_to = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render()
    {
        $assets = Asset::with('category')
            ->where('status', '!=', \App\Enums\AssetStatus::LOST)
            ->orderBy('name')
            ->get()
            ->map(function ($asset) {
                return (object) [
                    'value' => $asset->id,
                    'label' => $asset->name . ' (' . $asset->code . ') - ' . ($asset->category->name ?? 'Tanpa Kategori'),
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
                'label' => $user->name . ' (' . $user->role->value . ')',
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