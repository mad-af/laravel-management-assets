<?php

namespace App\Livewire\AssetLoans;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'asset_loan_id')] // ?asset_loan_id=123
    public ?string $asset_loan_id = null;

    #[Url(as: 'asset_id')] // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;

    public ?string $editingAssetLoanId = null;

    public ?string $editingAssetId = null;

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
            $this->editingAssetId = $this->asset_id;
        } elseif ($this->action === 'edit' && $this->asset_loan_id) {
            $this->showDrawer = true;
            $this->editingAssetLoanId = $this->asset_loan_id;
            $this->editingAssetId = $this->asset_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($assetLoanId)
    {
        $this->action = 'edit';
        $this->asset_loan_id = $assetLoanId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($assetId = '')
    {
        if (! empty($assetId)) {
            $this->asset_id = $assetId;
        }

        $this->action = 'create';
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingAssetLoanId = null;
        $this->editingAssetId = null;
        // $this->dispatch('reset-form');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->asset_loan_id = null;
        $this->asset_id = null;
    }

    public function editAssetLoan($assetLoanId)
    {
        $this->editingAssetLoanId = $assetLoanId;
        $this->showDrawer = true;
    }

    public function render()
    {
        return view('livewire.asset-loans.drawer');
    }
}
