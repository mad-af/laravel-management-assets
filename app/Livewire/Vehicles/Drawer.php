<?php

namespace App\Livewire\Vehicles;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=save-profile|save-odometer
    public ?string $action = null;

    #[Url(as: 'asset_id')]  // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;
    public ?string $assetId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-profile-drawer' => 'openProfileDrawer',
        'open-odometer-drawer' => 'openOdometerDrawer',
        'asset-id-changed' => 'handleAssetIdChanged'
    ];

    public function handleAssetIdChanged($assetId)
    {
        $this->asset_id = $assetId;
    }

    public function isActionSaveProfile(): bool
    {
        return $this->action === 'save-profile';
    }

    public function isActionSaveOdometer(): bool
    {
        return $this->action === 'save-odometer';
    }

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
        $isDrawerAction = in_array($this->action, [
                'save-profile', 
                'save-odometer',
            ]);

        if ($isDrawerAction && $this->asset_id) {
            $this->showDrawer = true;
            $this->assetId = $this->asset_id;
        } elseif ($isDrawerAction) {
            $this->showDrawer = true;
            $this->assetId = null;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openProfileDrawer(?string $assetId = null)
    {
        $this->action = 'save-profile';
        $this->asset_id = $assetId;
        $this->applyActionFromUrl();
    }

    public function openOdometerDrawer(?string $assetId = null)
    {
        $this->action = 'save-odometer';
        $this->asset_id = $assetId;
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->redirect(route('vehicles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.vehicles.drawer');
    }
}
