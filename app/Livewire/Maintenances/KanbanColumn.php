<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Models\AssetMaintenance;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh-kanban')]
class KanbanColumn extends Component
{
    public $status;

    public $title;

    public $maintenances;

    public $badgeColorClass;

    public bool $isLast = true;

    public function mount($status, $isLast)
    {
        $this->status = $status;
        $this->title = $status->label();
        $this->loadMaintenances();
        $this->badgeColorClass = 'badge-'.$status->color();
        $this->isLast = $isLast;
    }

    public function loadMaintenances()
    {
        $this->maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
            ->where('status', $this->status)
            ->orderBy('priority')
            ->orderBy('scheduled_date')
            ->get();
    }

    public function openEditDrawer($maintenanceId)
    {
        $this->dispatch('open-edit-drawer', $maintenanceId);
    }

    public function getAvailableStatuses(): object
    {
        $availableNextStatuses = [];
        $availablePreviousStatuses = [];
        $currentOrder = $this->status->order();

        // Dapatkan semua status
        $allStatuses = MaintenanceStatus::cases();

        foreach ($allStatuses as $status) {
            // Skip status yang sama dengan current status
            if ($status === $this->status) {
                continue;
            }

            $statusOrder = $status->order();

            // Logika next dan previous berdasarkan order:
            // - Previous: order yang lebih kecil 1 angka dari current
            // - Next: order yang lebih besar 1 angka dari current
            if ($statusOrder === $currentOrder - 1) {
                $availablePreviousStatuses[] = $status;
            } elseif ($statusOrder === $currentOrder + 1) {
                $availableNextStatuses[] = $status;
            }
        }

        return (object) [
            'next' => $availableNextStatuses,
            'previous' => $availablePreviousStatuses,
        ];
    }

    public function moveToStatus($maintenanceId, $newStatus)
    {
        $maintenance = AssetMaintenance::findOrFail($maintenanceId);
        $maintenance->update(['status' => $newStatus]);

        $this->dispatch('reload-page');
    }

    public function canPrintReport()
    {
        // Laporan tidak bisa dicetak jika status adalah COMPLETED atau CANCELLED
        return ! in_array($this->status, [MaintenanceStatus::COMPLETED, MaintenanceStatus::CANCELLED]);
    }

    public function canEdit()
    {
        // Laporan tidak bisa diedit jika status adalah COMPLETED atau CANCELLED
        return in_array($this->status, [MaintenanceStatus::OPEN]);
    }

    public function render()
    {
        return view('livewire.maintenances.kanban-column');
    }
}
