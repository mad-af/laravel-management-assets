<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceType;
use App\Models\AssetMaintenance;
use App\Support\MaintenanceServiceCheck;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh-kanban')]
class KanbanCard extends Component
{
    public AssetMaintenance $maintenance;

    public function mount(AssetMaintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function openEditDrawer()
    {
        $this->dispatch('open-edit-drawer', $this->maintenance->id);
    }

    public function render()
    {
        // Same late-service rule as the maintenance export (only preventive is judged)
        $serviceCheck = $this->maintenance->type === MaintenanceType::PREVENTIVE
            ? MaintenanceServiceCheck::for($this->maintenance)
            : MaintenanceServiceCheck::EMPTY;

        return view('livewire.maintenances.kanban-card', [
            'serviceCheck' => $serviceCheck,
            'isServiceLate' => MaintenanceServiceCheck::isLate($serviceCheck),
        ]);
    }
}
