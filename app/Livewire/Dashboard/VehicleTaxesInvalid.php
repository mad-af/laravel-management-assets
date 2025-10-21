<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Support\SessionKey;
use Livewire\Component;

class VehicleTaxesInvalid extends Component
{
    public function getInvalidVehicles()
    {
        $branchId = session_get(SessionKey::BranchId);
        return Asset::query()
            ->forBranch($branchId)
            ->vehicles()
            ->notValid()
            ->with('vehicleProfile')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        $invalidVehicles = $this->getInvalidVehicles();
        $count = $invalidVehicles->count();

        return view('livewire.dashboard.vehicle-taxes-invalid', compact('invalidVehicles', 'count'));
    }
}