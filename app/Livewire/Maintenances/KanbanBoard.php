<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Models\AssetMaintenance;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class KanbanBoard extends Component
{
    protected $listeners = [
        'maintenance-saved' => '$refresh',
        'refresh-kanban-board' => '$refresh',
    ];

    public function refreshBoard()
    {
        Log::info('KanbanBoard: refreshBoard method called');
        $this->render();
    }

    public function render()
    {
        // Get all maintenance statuses
        $statuses = MaintenanceStatus::cases();

        // Create status columns with maintenances
        $statusColumns = [];
        foreach ($statuses as $status) {
            $maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
                ->where('status', $status)
                ->orderBy('priority')
                ->orderBy('scheduled_date')
                ->get();

            $statusColumns[] = [
                'status' => $status,
                'maintenances' => $maintenances,
            ];
        }

        return view('livewire.maintenances.kanban-board', [
            'statusColumns' => $statusColumns,
        ]);
    }
}
