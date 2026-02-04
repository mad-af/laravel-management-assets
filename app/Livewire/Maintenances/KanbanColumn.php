<?php

namespace App\Livewire\Maintenances;

use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\AssetMaintenance;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Auth;
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
        // Get current branch ID from session
        $currentBranchId = session_get(SessionKey::BranchId);

        $status = $this->status;

        $this->maintenances = AssetMaintenance::with(['asset', 'assignedUser'])
            ->whereHas('asset', function ($query) use ($currentBranchId) {
                $query->when($currentBranchId, function ($q) use ($currentBranchId) {
                    $q->where('branch_id', $currentBranchId);
                });
            })
            ->where('status', $status)
            ->when(in_array($status, [MaintenanceStatus::COMPLETED, MaintenanceStatus::CANCELLED]), function ($q) use ($status) {
                $oneMonthAgo = now()->subMonth();

                // Ambil kode maintenance yang harus selalu ditampilkan dari config/env
                $alwaysShowCodesRaw = env('KANBAN_ALWAYS_SHOW_CODES', '');
                $alwaysShowCodes = $alwaysShowCodesRaw 
                    ? array_filter(array_map('trim', explode(',', $alwaysShowCodesRaw))) 
                    : [];

                // Debug log untuk memastikan config terbaca
                \Illuminate\Support\Facades\Log::info('KanbanColumn Debug', [
                    'status' => $status->value,
                    'config_raw' => $alwaysShowCodesRaw,
                    'parsed_codes' => $alwaysShowCodes
                ]);

                $q->where(function($query) use ($status, $oneMonthAgo, $alwaysShowCodes) {
                    $query->where(function($dateQuery) use ($status, $oneMonthAgo) {
                        if ($status === MaintenanceStatus::COMPLETED) {
                            $dateQuery->where('completed_at', '>=', $oneMonthAgo);
                        } else {
                            // Untuk CANCELLED gunakan updated_at sebagai acuan waktu
                            $dateQuery->where('updated_at', '>=', $oneMonthAgo);
                        }
                    });

                    if (!empty($alwaysShowCodes)) {
                        $query->orWhereIn('code', $alwaysShowCodes);
                    }
                });
            })
            ->orderBy('priority')
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
        $currentUser = Auth::user();

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
                // Cek jika ingin kembali dari COMPLETED atau CANCELLED ke IN_PROGRESS
                // Hanya admin yang bisa melakukan ini
                if (($this->status === MaintenanceStatus::COMPLETED || $this->status === MaintenanceStatus::CANCELLED)
                    && $status === MaintenanceStatus::IN_PROGRESS) {
                    if ($currentUser && $currentUser->role === UserRole::ADMIN) {
                        $availablePreviousStatuses[] = $status;
                    }
                    // Jika bukan admin, skip status ini
                } else {
                    $availablePreviousStatuses[] = $status;
                }
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
        if ($newStatus === MaintenanceStatus::COMPLETED->value) {
            $this->dispatch('open-complete-drawer', maintenanceId: $maintenanceId);
            // $this->dispatch('open-completed-drawer', $maintenanceId);
        } else {
            $maintenance = AssetMaintenance::findOrFail($maintenanceId);
            $maintenance->update(['status' => $newStatus]);

            $this->dispatch('reload-page');
        }
    }

    public function canPrintReport()
    {
        // Laporan tidak bisa dicetak jika status adalah CANCELLED
        return ! in_array($this->status, [MaintenanceStatus::CANCELLED]);
    }

    public function canEdit()
    {
        // Laporan tidak bisa diedit jika status adalah COMPLETED atau CANCELLED
        return in_array($this->status, [MaintenanceStatus::OPEN]);
    }

    public function openCompletedDrawer($maintenanceId)
    {
        $this->dispatch('open-completed-drawer', maintenanceId: $maintenanceId);
    }

    public function render()
    {
        return view('livewire.maintenances.kanban-column');
    }
}
