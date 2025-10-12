<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use Livewire\Component;

class VehicleHistory extends Component
{
    public Asset $vehicle;

    public string $activeTab = 'tax';

    public function mount(Asset $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function getOdometerLogs()
    {
        return $this->vehicle->odometerLogs()
            ->orderBy('read_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getMaintenances()
    {
        return $this->vehicle->maintenances()
            ->orderBy('scheduled_date', 'desc')
            ->limit(10)
            ->get();
    }

    public function getTaxHistories()
    {
        return $this->vehicle->vehicleTaxHistories()
            ->with('vehicleTaxType')
            ->orderBy('paid_date', 'asc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.vehicles.vehicle-history', [
            'odometerLogs' => $this->getOdometerLogs(),
            'maintenances' => $this->getMaintenances(),
            'taxHistories' => $this->getTaxHistories(),

        ]);
    }
}
