<?php

namespace App\Livewire\Maintenances;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit|complete
    public ?string $action = null;

    #[Url(as: 'maintenance_id')]  // ?maintenance_id=123
    public ?string $maintenance_id = null;

    public bool $showDrawer = false;
    public ?string $editingMaintenanceId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'open-complete-drawer' => 'openCompleteDrawer',
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

    public function updatedMaintenanceId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingMaintenanceId = null;
        } elseif ($this->action === 'edit' && $this->maintenance_id) {
            $this->showDrawer   = true;
            $this->editingMaintenanceId = $this->maintenance_id;
        } elseif ($this->action === 'complete' && $this->maintenance_id) {
            $this->showDrawer   = true;
            $this->editingMaintenanceId = $this->maintenance_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openCompleteDrawer($maintenanceId)
    {
        $this->action = 'complete';
        $this->maintenance_id = $maintenanceId;
        $this->applyActionFromUrl();
    }

    public function openEditDrawer($maintenanceId)
    {
        $this->action = 'edit';
        $this->maintenance_id = $maintenanceId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($maintenanceId = null)
    {
        if ($maintenanceId) {
            $this->action = 'edit';
            $this->maintenance_id = $maintenanceId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingMaintenanceId = null;
        // $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->maintenance_id = null;
    }

    public function render()
    {
        return view('livewire.maintenances.drawer');
    }
}