<?php

namespace App\Livewire\AssetLoans;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingAssetLoanId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editAssetLoan' => 'editAssetLoan',
        'asset-loan-saved' => 'handleAssetLoanSaved',
        'close-drawer' => 'closeDrawer'
    ];

    public function openDrawer()
    {
        $this->showDrawer = true;
        $this->editingAssetLoanId = null;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingAssetLoanId = null;
        $this->dispatch('resetForm');
    }

    public function editAssetLoan($assetLoanId)
    {
        $this->editingAssetLoanId = $assetLoanId;
        $this->showDrawer = true;
    }

    public function handleAssetLoanSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.asset-loans.drawer');
    }
}