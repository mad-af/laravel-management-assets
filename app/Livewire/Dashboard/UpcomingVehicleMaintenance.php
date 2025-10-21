<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Support\SessionKey;
use Livewire\Component;

class UpcomingVehicleMaintenance extends Component
{
    public function getVehicles()
    {
        $branchId = session_get(SessionKey::BranchId);

        $vehicles = Asset::query()
            ->forBranch($branchId)
            ->vehicles()
            ->with('vehicleProfile')
            ->get();

        // Filter yang punya jadwal/target selanjutnya
        $vehicles = $vehicles->filter(function ($asset) {
            return $asset->vehicleProfile && (
                $asset->vehicleProfile->next_service_date || $asset->vehicleProfile->service_target_odometer_km
            );
        })->sortBy(function ($asset) {
            // Urutkan berdasarkan tanggal terdekat atau selisih km menuju target
            $vp = $asset->vehicleProfile;
            $dateScore = $vp->next_service_date ? $vp->next_service_date->timestamp : PHP_INT_MAX;
            $kmDelta = $vp->service_target_odometer_km && $vp->current_odometer_km
                ? max(0, $vp->service_target_odometer_km - $vp->current_odometer_km)
                : PHP_INT_MAX;
            return [$dateScore, $kmDelta];
        })->take(8);

        return $vehicles;
    }

    public function render()
    {
        $vehicles = $this->getVehicles();
        $count = $vehicles->count();

        return view('livewire.dashboard.upcoming-vehicle-maintenance', compact('vehicles', 'count'));
    }
}