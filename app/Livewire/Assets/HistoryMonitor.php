<?php

namespace App\Livewire\Assets;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryMonitor extends Component
{
    use WithPagination;

    public Asset $asset;

    public bool $showAll = false;

    public int $perPage = 20;

    public ?string $statusFilter = null;

    public ?string $typeFilter = null;

    public ?string $priorityFilter = null;

    public string $search = '';

    public array $expanded = [];

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function toggleShowAll(): void
    {
        $this->showAll = ! $this->showAll;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->statusFilter = null;
        $this->typeFilter = null;
        $this->priorityFilter = null;
        $this->search = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AssetMaintenance::where('asset_id', $this->asset->id)
            ->with(['employee'])
            ->orderBy('created_at', 'desc')
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->priorityFilter, function ($q) {
                $q->where('priority', $this->priorityFilter);
            })
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($inner) use ($search) {
                    $inner->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('vendor_name', 'like', "%{$search}%")
                        ->orWhere('technician_name', 'like', "%{$search}%");
                });
            });

        if (! $this->showAll) {
            $maintenances = $query->limit(10)->get();
        } else {
            $maintenances = $query->paginate($this->perPage);
        }

        return view('livewire.assets.history-monitor', [
            'maintenances' => $maintenances,
            'statuses' => MaintenanceStatus::cases(),
            'types' => MaintenanceType::cases(),
            'priorities' => MaintenancePriority::cases(),
        ]);
    }
}
