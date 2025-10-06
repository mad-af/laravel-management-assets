<?php

namespace App\Livewire\Maintenances;

use App\Models\AssetMaintenance;
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
        return view('livewire.maintenances.kanban-card');
    }
}
