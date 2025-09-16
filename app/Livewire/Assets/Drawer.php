<?php

namespace App\Livewire\Assets;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingAssetId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editAsset' => 'editAsset',
        'asset-saved' => 'handleAssetSaved',
        'close-drawer' => 'closeDrawer'
    ];

    public function openDrawer()
    {
        $this->showDrawer = true;
        $this->editingAssetId = null;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingAssetId = null;
        $this->dispatch('resetForm');
    }

    public function editAsset($assetId)
    {
        $this->editingAssetId = $assetId;
        $this->showDrawer = true;
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