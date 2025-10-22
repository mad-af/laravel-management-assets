<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Support\SessionKey;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    public function formatOdometerTargetInfo($currentKm, $targetKm)
    {
        if ($targetKm === null) {
            return null;
        }

        try {
            $current = $currentKm ?? 0;
            $target = $targetKm;
            $diff = $target - $current;
            $isOverdue = $diff <= 0;

            if ($isOverdue) {
                $distanceInfo = $diff === 0
                    ? 'Sudah mencapai target'
                    : 'Terlampaui '.number_format(abs($diff), 0, ',', '.').' km';
            } else {
                $distanceInfo = 'Sisa '.number_format($diff, 0, ',', '.').' km';
            }

            return [
                'distance_info' => $distanceInfo,
                'is_overdue' => $isOverdue,
                'diff_km' => $diff,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function formatNextServiceDate($nextServiceDate)
    {
        if (! $nextServiceDate) {
            return null;
        }

        try {
            // Pakai locale per instance (tanpa setLocale global)
            $serviceDate = Carbon::parse($nextServiceDate)->locale('id');
            $now = Carbon::now($serviceDate->timezone);

            // Format tanggal dengan nama bulan Indonesia - gunakan 'j M Y' untuk menghindari duplikasi
            $formattedDate = $serviceDate->translatedFormat('j M Y');

            // Same-day
            if ($serviceDate->isSameDay($now)) {
                return [
                    'formatted_date' => $formattedDate,
                    'time_info' => 'Hari ini',
                    'is_overdue' => false,
                    'days_left' => 0,
                ];
            }

            // Buat frasa human-friendly tanpa awalan/akhiran ("2 bulan 3 hari")
            $span = $serviceDate->diffForHumans($now, [
                'parts' => 2,
                'join' => true,
                'short' => false,
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ]);

            $isOverdue = $serviceDate->lessThan($now);
            $timeInfo = $isOverdue ? "$span yang lalu" : "$span lagi";

            // Selisih hari integer (tanpa pecahan), dinormalisasi ke awal hari
            $daysLeft = $now->startOfDay()->diffInDays($serviceDate->startOfDay(), false);

            return [
                'formatted_date' => $formattedDate,
                'time_info' => $timeInfo,
                'is_overdue' => $isOverdue,
                'days_left' => $daysLeft,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function render()
    {
        $vehicles = $this->getVehicles();
        $count = $vehicles->count();

        return view('livewire.dashboard.upcoming-vehicle-maintenance', compact('vehicles', 'count'));
    }
}
