<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\AssetMaintenance;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $priorityFilter = '';
    public int $perPage = 15;

    public array $selectedMaintenances = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AssetMaintenance::with(['asset', 'employee'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('vendor_name', 'like', "%{$search}%")
                    ->orWhere('technician_name', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($qa) use ($search) {
                        $qa->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $maintenances = $query->paginate($this->perPage);

        return view('livewire.maintenances.table', [
            'maintenances' => $maintenances,
            'statuses' => MaintenanceStatus::cases(),
            'types' => MaintenanceType::cases(),
            'priorities' => MaintenancePriority::cases(),
        ]);
    }
}