<?php

namespace App\Livewire\Locations;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingLocationId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'location-saved' => 'closeDrawer',
    ];

    public function openDrawer($locationId = null)
    {
        $this->editingLocationId = $locationId;
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingLocationId = null;
        $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.locations.drawer');
    }
}