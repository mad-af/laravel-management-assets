<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Exports\MaintenancesMonthlyExport;
use App\Models\Branch;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[On('refresh-kanban')]
class KanbanBoard extends Component
{
    #[On('download-asset-maintenance')]
    public function downloadAssetMaintenance()
    {
        $branchId = session_get(SessionKey::BranchId);
        $branchName = optional(Branch::find($branchId))->name ?? 'Cabang';
        $filename = 'Maintenance_'.str_replace(' ', '_', $branchName).'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        try {
            return Excel::download(new MaintenancesMonthlyExport($branchId), $filename);
        } catch (\Exception $e) {
            Log::error('Gagal mengunduh maintenance Excel: '.$e->getMessage());

            return null;
        }
    }

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
