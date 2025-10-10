<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\AssetMaintenance;
use App\Models\AssetTransfer;
use Carbon\Carbon;
use Livewire\Component;

class StatsCard extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function getAssetAge()
    {
        if (! $this->asset->purchase_date) {
            return 'Belum diketahui';
        }

        $purchaseDate = Carbon::parse($this->asset->purchase_date);
        $now = Carbon::now();

        $diffInDays = $purchaseDate->diffInDays($now);
        $diffInMonths = $purchaseDate->diffInMonths($now);
        $diffInYears = intval($purchaseDate->diffInYears($now));

        // Format ringkas dalam bahasa Indonesia
        if ($diffInYears > 0) {
            $remainingMonths = $diffInMonths % 12;
            if ($remainingMonths > 0) {
                return "{$diffInYears} thn {$remainingMonths} bln";
            }

            return "{$diffInYears} tahun";
        } elseif ($diffInMonths > 0) {
            return "{$diffInMonths} bulan";
        } elseif ($diffInDays > 0) {
            return "{$diffInDays} hari";
        } else {
            return 'Baru dibeli';
        }
    }

    public function getTotalMaintenances()
    {
        return AssetMaintenance::where('asset_id', $this->asset->id)->count();
    }

    public function getTotalTransfers()
    {
        return AssetTransfer::whereHas('items', function ($query) {
            $query->where('asset_id', $this->asset->id);
        })->count();
    }

    public function getTotalLogs()
    {
        return AssetLog::where('asset_id', $this->asset->id)->count();
    }

    public function getLastMaintenanceDate()
    {
        $lastMaintenance = AssetMaintenance::where('asset_id', $this->asset->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $lastMaintenance) {
            return 'Belum ada maintenance';
        }

        return Carbon::parse($lastMaintenance->created_at)->locale('id')->diffForHumans();
    }

    public function render()
    {
        return view('livewire.assets.stats-card');
    }
}
