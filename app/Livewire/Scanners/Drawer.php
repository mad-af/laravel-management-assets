<?php

namespace App\Livewire\Scanners;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    const ACTION_MAINTENANCE = 'maintenance';

    const ACTION_CHECK_OUT = 'check-out';

    const ACTION_CHECK_IN = 'check-in';

    #[Url(as: 'action')] // ?action=maintenance|check-out|check-in
    public ?string $action = null;

    #[Url(as: 'asset_id')] // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl();
    }

    public function updated($property)
    {
        if (in_array($property, ['action', 'asset_id'])) {
            $this->applyActionFromUrl();
        }
    }

    protected function applyActionFromUrl(): void
    {
        // if ($this->action === 'create') {
        //     $this->showDrawer = true;
        // } elseif ($this->action === 'edit' && $this->scanner_id) {
        //     $this->showDrawer = true;
        // } // else: biarkan state tetap (jangan auto-tutup tiap update)

        if ($this->action) {
            $this->showDrawer = true;
        }

    }

    #[On('drawer:openDrawerMaintenance')]
    public function openDrawerMaintenance($assetId)
    {
        $this->action = self::ACTION_MAINTENANCE;
        $this->asset_id = $assetId;
        $this->applyActionFromUrl();
    }

    #[On('drawer:openDrawerCheckOut')]
    public function openDrawerCheckOut($assetId)
    {
        $this->action = self::ACTION_CHECK_OUT;
        $this->asset_id = $assetId;
        $this->applyActionFromUrl();
    }

    #[On('drawer:openDrawerCheckIn')]
    public function openDrawerCheckIn($assetId)
    {
        $this->action = self::ACTION_CHECK_IN;
        $this->asset_id = $assetId;
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->redirect(route('scanners.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.scanners.drawer');
    }
}
