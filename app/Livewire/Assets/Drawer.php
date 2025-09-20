<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'asset_id')]  // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;
    public ?string $editingAssetId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'asset-saved' => 'handleAssetSaved',
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

    public function updatedAssetId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingAssetId = null;
        } elseif ($this->action === 'edit' && $this->asset_id) {
            $this->showDrawer   = true;
            $this->editingAssetId = $this->asset_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($assetId)
    {
        $this->action = 'edit';
        $this->asset_id = $assetId;
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
        $this->editingAssetId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->asset_id = null;
    }

    public function editAsset($assetId)
    {
        $this->openEditDrawer($assetId);
    }

    public function handleAssetSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.assets.drawer');
    }
}