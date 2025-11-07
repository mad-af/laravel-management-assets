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
        'open-batch-drawer' => 'openBatchDrawer',
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
        } elseif ($this->action === 'batch') {
            $this->showDrawer = true;
            $this->editingAssetId = null;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($assetId)
    {
        $this->redirect(route('assets.index', [
            'action' => 'edit',
            'asset_id' => $assetId,
        ]), navigate: true);
    }

    public function openDrawer()
    {
        $this->redirect(route('assets.index', [
            'action' => 'create',
        ]), navigate: true);
    }

    public function openBatchDrawer()
    {
        $this->redirect(route('assets.index', [
            'action' => 'batch',
        ]), navigate: true);
    }

    public function closeDrawer()
    {
        $this->redirect(route('assets.index'), navigate: true);
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