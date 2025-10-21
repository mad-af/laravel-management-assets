<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Support\SessionKey;
use Livewire\Component;

class VehicleTaxesDue extends Component
{
    public function getOverdue()
    {
        $branchId = session_get(SessionKey::BranchId);
        return Asset::query()->forBranch($branchId)->vehicles()->overdue()->with('vehicleProfile')->limit(6)->get();
    }

    public function getDueSoon()
    {
        $branchId = session_get(SessionKey::BranchId);
        return Asset::query()->forBranch($branchId)->vehicles()->dueSoon()->with('vehicleProfile')->limit(6)->get();
    }

    public function render()
    {
        $overdue = $this->getOverdue();
        $dueSoon = $this->getDueSoon();
        $overdueCount = $overdue->count();
        $dueSoonCount = $dueSoon->count();

        return view('livewire.dashboard.vehicle-taxes-due', compact('overdue', 'dueSoon', 'overdueCount', 'dueSoonCount'));
    }
}