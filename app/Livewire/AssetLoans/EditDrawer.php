<?php

namespace App\Livewire\AssetLoans;

use App\Models\AssetLoan;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $assetLoan;
    public $assetLoanId;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeEditDrawer' => 'closeDrawer',
        'asset-loan-updated' => 'handleAssetLoanUpdated'
    ];

    public function openDrawer($assetLoanId)
    {
        $this->assetLoanId = $assetLoanId;
        $this->assetLoan = AssetLoan::with(['asset', 'asset.category', 'asset.location'])->find($assetLoanId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->assetLoan = null;
        $this->assetLoanId = null;
        $this->dispatch('resetEditForm');
    }

    public function handleAssetLoanUpdated()
    {
        $this->closeDrawer();
        $this->dispatch('asset-loan-saved'); // Refresh table
    }

    public function render()
    {
        return view('livewire.asset-loans.edit-drawer');
    }
}