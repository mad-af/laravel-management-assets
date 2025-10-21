<?php

namespace App\Livewire\Dashboard;

use App\Enums\AssetTransferStatus;
use App\Models\AssetTransfer;
use App\Support\SessionKey;
use Livewire\Component;

class TransfersNeedConfirmation extends Component
{
    public function getTransfers()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        return AssetTransfer::query()
            ->with('fromBranch') 
            ->confirmationAction($currentBranchId)
            ->where('status', AssetTransferStatus::SHIPPED)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        $transfers = $this->getTransfers();
        $count = $transfers->count();

        return view('livewire.dashboard.transfers-need-confirmation', compact('transfers', 'count'));
    }
}