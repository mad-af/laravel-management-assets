<?php

namespace App\Livewire\VehicleTaxes;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'vehicle_tax_id')] // ?vehicle_tax_id=123
    public ?string $vehicle_tax_id = null;

    public bool $showDrawer = false;

    public ?string $editingVehicleTaxId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'vehicle-tax-saved' => 'closeDrawer',
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

    public function updatedVehicleTaxId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingVehicleTaxId = null;
        } elseif ($this->action === 'edit' && $this->vehicle_tax_id) {
            $this->showDrawer = true;
            $this->editingVehicleTaxId = $this->vehicle_tax_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($vehicleTaxId)
    {
        $this->action = 'edit';
        $this->vehicle_tax_id = $vehicleTaxId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($vehicleTaxId = null)
    {
        if ($vehicleTaxId) {
            $this->action = 'edit';
            $this->vehicle_tax_id = $vehicleTaxId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingVehicleTaxId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->vehicle_tax_id = null;
    }

    public function editVehicleTax($vehicleTaxId)
    {
        $this->openEditDrawer($vehicleTaxId);
        $this->showDrawer = true;
    }

    public function handleVehicleTaxSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.vehicle-taxes.drawer');
    }
}