<?php

namespace App\Livewire\Maintenances;

use Livewire\Component;

class KanbanColumn extends Component
{
    public $status;

    public $title;

    public $maintenances;

    public $badgeColorClass;

    public function mount($status, $maintenances)
    {
        $this->status = $status;
        $this->title = $status->label();
        $this->maintenances = $maintenances;
        $this->badgeColorClass = 'badge-'.$status->color();
    }

    public function openEditDrawer($maintenanceId)
    {
        $this->dispatch('open-edit-drawer', $maintenanceId);
    }

    public function render()
    {
        return view('livewire.maintenances.kanban-column');
    }
}
