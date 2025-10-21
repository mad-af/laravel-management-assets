<?php

namespace App\Livewire\Dashboard;

use App\Models\AssetLoan;
use App\Support\SessionKey;
use Livewire\Component;

class OverdueBorrowers extends Component
{
    public function getOverdueLoans()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        return AssetLoan::query()
            ->overdue()
            ->with(['asset', 'employee'])
            ->whereHas('asset', function ($q) use ($currentBranchId) {
                $q->forBranch($currentBranchId);
            })
            ->orderBy('due_at')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        $loans = $this->getOverdueLoans();
        $count = $loans->count();

        return view('livewire.dashboard.overdue-borrowers', compact('loans', 'count'));
    }
}