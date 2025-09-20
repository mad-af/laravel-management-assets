<?php

namespace App\Livewire\Locations;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'location_id')]  // ?location_id=123
    public ?string $location_id = null;

    public bool $showDrawer = false;
    public ?string $editingLocationId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'location-saved' => 'closeDrawer',
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

    public function updatedLocationId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingLocationId = null;
        } elseif ($this->action === 'edit' && $this->location_id) {
            $this->showDrawer   = true;
            $this->editingLocationId = $this->location_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($locationId)
    {
        $this->action = 'edit';
        $this->location_id = $locationId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($locationId = null)
    {
        if ($locationId) {
            $this->action = 'edit';
            $this->location_id = $locationId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingLocationId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->location_id = null;
    }

    public function render()
    {
        return view('livewire.locations.drawer');
    }
}