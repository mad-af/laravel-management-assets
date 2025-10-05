<?php

namespace App\Livewire\Maintenances;

use App\Models\AssetMaintenance;
use Livewire\Component;

class KanbanCard extends Component
{
    public AssetMaintenance $maintenance;

    public function mount(AssetMaintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function render()
    {
        return view('livewire.maintenances.kanban-card');
    }
}
