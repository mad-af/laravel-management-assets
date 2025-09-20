<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use Livewire\Component;

class VehicleHistory extends Component
{
    public Asset $vehicle;

    public string $activeTab = 'odometer';

    public function mount(Asset $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function getOdometerLogs()
    {
        return $this->vehicle->odometerLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.vehicles.vehicle-history', [
            'odometerLogs' => $this->getOdometerLogs()
        ]);
    }
}