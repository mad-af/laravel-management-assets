<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh-kanban')]
class KanbanBoard extends Component
{
    public function render()
    {
        $statuses = MaintenanceStatus::cases();

        // Create status columns
        $statusColumns = [];
        foreach ($statuses as $status) {
            $statusColumns[] = [
                'status' => $status,
            ];
        }

        return view('livewire.maintenances.kanban-board', [
            'statusColumns' => $statusColumns,
        ]);
    }
}
