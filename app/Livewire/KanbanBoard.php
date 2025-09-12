<?php

namespace App\Livewire;

use App\Enums\MaintenanceStatus;
use App\Models\AssetMaintenance;
use Livewire\Component;

class KanbanBoard extends Component
{
    public $statusColumns = [];

    public function mount()
    {
        $this->loadMaintenances();
    }

    public function loadMaintenances()
    {
        $maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->statusColumns = collect(MaintenanceStatus::cases())->map(function ($status) use ($maintenances) {
            return [
                'status' => $status,
                'maintenances' => $maintenances->where('status', $status)
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.kanban-board');
    }
}
