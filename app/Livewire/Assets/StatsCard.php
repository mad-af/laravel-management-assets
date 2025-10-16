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

    public bool $isHorizontal = false;

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

        $diffInYears = intval($purchaseDate->diffInYears($now));
        $diffInMonths = $purchaseDate->diffInMonths($now);
        $diffInDays = $purchaseDate->diffInDays($now);
        $diffInHours = $purchaseDate->diffInHours($now);

        // Format ringkas dalam bahasa Indonesia
        // Format waktu ringkas dalam Bahasa Indonesia tanpa desimal
        $diffInYears = floor($diffInYears);
        $diffInMonths = floor($diffInMonths);
        $diffInDays = floor($diffInDays);
        $diffInHours = floor($diffInHours);

        if ($diffInYears > 0) {
            $remainingMonths = $diffInMonths % 12;
            if ($remainingMonths > 0) {
                return "{$diffInYears} thn {$remainingMonths} bln";
            }

            return "{$diffInYears} thn";
        }

        if ($diffInMonths > 0) {
            $remainingDays = $diffInDays % 30;
            if ($remainingDays > 0) {
                return "{$diffInMonths} bln {$remainingDays} hr";
            }

            return "{$diffInMonths} bln";
        }

        if ($diffInDays > 0) {
            $remainingHours = $diffInHours % 24;
            if ($remainingHours > 0) {
                return "{$diffInDays} hr {$remainingHours} jam";
            }

            return "{$diffInDays} hr";
        }

        return 'Baru dibeli';

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
