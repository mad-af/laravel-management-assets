<?php

namespace App\Livewire\Dashboard;

use App\Enums\InsuranceClaimStatus;
use App\Models\InsuranceClaim;
use App\Support\SessionKey;
use Livewire\Component;

class InsuranceClaimDraft extends Component
{
    public function getDraftClaims()
    {
        $branchId = session_get(SessionKey::BranchId);

        return InsuranceClaim::query()
            ->where('status', InsuranceClaimStatus::DRAFT->value)
            ->whereHas('asset', function ($q) use ($branchId) {
                $q->forBranch($branchId);
            })
            ->with(['asset.vehicleProfile', 'policy'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }
    
    public function render()
    {
        $claims = $this->getDraftClaims();
        $count = $claims->count();

        return view('livewire.dashboard.insurance-claim-draft',compact('claims', 'count'));
    }
}
