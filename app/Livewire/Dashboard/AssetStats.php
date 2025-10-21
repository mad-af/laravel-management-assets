<?php

namespace App\Livewire\Dashboard;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Support\SessionKey;
use Livewire\Component;

class AssetStats extends Component
{
    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        $totalAssets = Asset::forBranch($currentBranchId)->count();
        $activeAssets = Asset::forBranch($currentBranchId)->where('status', AssetStatus::ACTIVE)->count();
        $maintenanceAssets = Asset::forBranch($currentBranchId)->where('status', AssetStatus::MAINTENANCE)->count();
        $onLoanAssets = Asset::forBranch($currentBranchId)->where('status', AssetStatus::ON_LOAN)->count();
        $inTransferAssets = Asset::forBranch($currentBranchId)->where('status', AssetStatus::IN_TRANSFER)->count();
        $vehiclesCount = Asset::vehicles()->forBranch($currentBranchId)->count();

        return view('livewire.dashboard.asset-stats', compact(
            'totalAssets',
            'activeAssets',
            'maintenanceAssets',
            'onLoanAssets',
            'inTransferAssets',
            'vehiclesCount'
        ));
    }
}