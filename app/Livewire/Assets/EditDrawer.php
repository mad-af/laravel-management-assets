<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $asset;
    public $assetId;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeEditDrawer' => 'closeDrawer',
        'asset-updated' => 'handleAssetUpdated'
    ];

    public function openDrawer($assetId)
    {
        $this->assetId = $assetId;
        $this->asset = Asset::with(['category', 'location'])->find($assetId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->asset = null;
        $this->assetId = null;
        $this->dispatch('resetEditForm');
    }

    public function handleAssetUpdated()
    {
        $this->closeDrawer();
        $this->dispatch('asset-saved'); // Refresh table
    }

    public function render()
    {
        return view('livewire.assets.edit-drawer');
    }
}