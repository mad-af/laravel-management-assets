<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $location;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'location-updated' => 'closeDrawer',
    ];

    public function openDrawer($locationId)
    {
        $this->location = Location::find($locationId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->location = null;
    }

    public function render()
    {
        return view('livewire.locations.edit-drawer');
    }
}