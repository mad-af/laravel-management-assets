<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use Livewire\Component;

class VehicleProfileInfo extends Component
{
    public Asset $vehicle;

    public function mount(Asset $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function render()
    {
        return view('livewire.vehicles.vehicle-profile-info');
    }
}