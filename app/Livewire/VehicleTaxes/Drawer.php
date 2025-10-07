<?php

namespace App\Livewire\VehicleTaxes;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=tax-payment|tax-type
    public ?string $action = null;

    #[Url(as: 'asset_id')] // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        // 'open-edit-drawer' => 'openEditDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl(); // hanya sekali di initial load
    }

    // Dipanggil kalau kamu ubah action via property (akan auto update URL)
    public function updatedAction()
    {
        $this->applyActionFromUrl();
    }

    public function updatedVehicleTaxId()
    {
        $this->applyActionFromUrl();
    }

    public function updatedVehicleTaxTypeId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action) {
            $this->showDrawer = true;
        }
    }

    #[On('open-drawer')]
    public function openDrawer($assetId = null)
    {
        $this->action = 'tax-payment';
        if ($assetId) {
            $this->asset_id = $assetId;
        }
        $this->applyActionFromUrl();
    }

    #[On('open-tax-type-drawer')]
    public function openTaxTypeDrawer($assetId = null)
    {
        $this->action = 'tax-type';
        if ($assetId) {
            $this->asset_id = $assetId;
        }
        $this->applyActionFromUrl();
    }

    #[On('close-drawer')]
    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->asset_id = null;
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.drawer');
    }
}