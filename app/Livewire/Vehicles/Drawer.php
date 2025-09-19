<?php

namespace App\Livewire\Vehicles;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'vehicle_id')]  // ?vehicle_id=123
    public ?string $vehicle_id = null;

    public bool $showDrawer = false;
    public ?string $editingVehicleId = null;

    protected $listeners = [
        'closeDrawer' => 'closeDrawer',
        'vehicle-saved' => 'handleVehicleSaved',
        'vehicle-updated' => 'handleVehicleSaved',
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

    public function updatedVehicleId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingVehicleId = null;
        } elseif ($this->action === 'edit' && $this->vehicle_id) {
            $this->showDrawer   = true;
            $this->editingVehicleId = $this->vehicle_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($vehicleId)
    {
        $this->action = 'edit';
        $this->vehicle_id = $vehicleId;
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
        $this->editingVehicleId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->vehicle_id = null;
    }

    public function handleVehicleSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.vehicles.drawer');
    }
}
