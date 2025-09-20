<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use Livewire\Component;

class QuickActionsCard extends Component
{
    public Asset $vehicle;

    public function mount(Asset $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function editVehicle()
    {
        return redirect()->route('dashboard.vehicles.edit', $this->vehicle);
    }

    public function addOdometerLog()
    {
        $this->dispatch('open-profile-drawer', action: 'save-odometer', assetId: $this->vehicle->id);
    }

    public function editProfile()
    {
        $this->dispatch('open-profile-drawer', action: 'save-profile', assetId: $this->vehicle->id);
    }

    public function render()
    {
        return view('livewire.vehicles.quick-actions-card');
    }
}