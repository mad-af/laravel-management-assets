<?php

namespace App\Livewire\AssetLoans;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'asset_loan_id')]  // ?asset_loan_id=123
    public ?string $asset_loan_id = null;

    public bool $showDrawer = false;
    public ?string $editingAssetLoanId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl(); // hanya sekali di initial load
    }

    // Dipanggil kalau kamu ubah action via property (akan auto update URL)
    public function updatedAction($value)
    {
        $this->applyActionFromUrl();
    }

    public function updatedAssetLoanId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingAssetLoanId = null;
        } elseif ($this->action === 'edit' && $this->asset_loan_id) {
            $this->showDrawer   = true;
            $this->editingAssetLoanId = $this->asset_loan_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($assetLoanId)
    {
        $this->action = 'edit';
        $this->asset_loan_id = $assetLoanId;
        $this->applyActionFromUrl();
    }

    public function openDrawer()
    {
        $this->action = 'create';
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingAssetLoanId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->asset_loan_id = null;
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