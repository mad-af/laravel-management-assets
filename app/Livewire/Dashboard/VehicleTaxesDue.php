<?php

namespace App\Livewire\Dashboard;

use App\Enums\VehicleTaxStatus;
use App\Models\VehicleTaxHistory;
use App\Support\SessionKey;
use Livewire\Component;

class VehicleTaxesDue extends Component
{
    public bool $useDummy = false;

    // '', 'overdue', 'due_soon'
    public string $statusFilter = '';

    /**
     * Ambil pajak yang TERLAMBAT (unpaid, due_date < now) untuk branch aktif
     */
    public function getOverdueHistories()
    {
        if ($this->useDummy) {
            // Nonaktifkan dummy untuk konsistensi tipe data; gunakan query nyata
        }

        $branchId = session_get(SessionKey::BranchId);

        return VehicleTaxHistory::query()
            ->whereNull('paid_date')
            ->whereHas('asset', function ($q) use ($branchId) {
                $q->forBranch($branchId)->vehicles();
            })
            ->where('due_date', '<', now())
            ->with(['asset', 'vehicleTaxType'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Ambil pajak yang segera JATUH TEMPO (unpaid, due_date within next 30 days)
     */
    public function getDueSoonHistories()
    {
        if ($this->useDummy) {
            // Nonaktifkan dummy untuk konsistensi tipe data; gunakan query nyata
        }

        $branchId = session_get(SessionKey::BranchId);

        return VehicleTaxHistory::query()
            ->whereNull('paid_date')
            ->whereHas('asset', function ($q) use ($branchId) {
                $q->forBranch($branchId)->vehicles();
            })
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->with(['asset', 'vehicleTaxType'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Gabungkan items sesuai filter status
     */
    public function getItems()
    {
        $overdue = $this->getOverdueHistories();
        $dueSoon = $this->getDueSoonHistories();

        if ($this->statusFilter === 'overdue') {
            return $overdue;
        }
        if ($this->statusFilter === 'due_soon') {
            return $dueSoon;
        }

        return $overdue->merge($dueSoon);
    }

    /**
     * Presentasi data untuk tabel (hindari @php di Blade)
     */
    public function presentHistory($history): object
    {
        $status = $history->status; // accessor enum dari model

        $statusText = match ($status) {
            VehicleTaxStatus::OVERDUE => 'Terlambat',
            VehicleTaxStatus::DUE_SOON => 'Jatuh Tempo',
            VehicleTaxStatus::PAID => 'Dibayar',
            default => 'Akan Datang',
        };

        $badgeClass = match ($status) {
            VehicleTaxStatus::OVERDUE => 'badge badge-error',
            VehicleTaxStatus::DUE_SOON => 'badge badge-warning',
            VehicleTaxStatus::PAID => 'badge badge-success',
            default => 'badge',
        };

        $dueHuman = $history->due_date
            ? $history->due_date->locale('id')->diffForHumans()
            : '-';

        $dueTextClass = match ($status) {
            VehicleTaxStatus::OVERDUE => 'text-error',
            VehicleTaxStatus::DUE_SOON => 'text-warning',
            default => '',
        };

        return (object) [
            'vehicle_name' => $history->asset?->name ?? '-',
            'tax_type_label' => $history->vehicleTaxType?->tax_type?->label() ?? '-',
            'due_date' => $dueHuman,
            'due_text_class' => $dueTextClass,
            'status_text' => $statusText,
            'status_badge' => $badgeClass,
        ];
    }

    public function render()
    {
        $histories = $this->getItems();
        $items = $histories->map(fn ($h) => $this->presentHistory($h));
        $count = $items->count();

        return view('livewire.dashboard.vehicle-taxes-due', compact('items', 'count'))
            ->with('statusFilter', $this->statusFilter);
    }
}
