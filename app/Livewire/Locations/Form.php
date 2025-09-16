<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $locationId;
    public $name = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $postal_code = '';
    public $is_active = true;
    public $isEdit = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:500',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:20',
        'is_active' => 'boolean',
    ];

    protected $listeners = [
        'editLocation' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($locationId = null)
    {
        $this->locationId = $locationId;
        
        if ($locationId) {
            $this->isEdit = true;
            $this->loadLocation();
        }
    }

    public function loadLocation()
    {
        if ($this->locationId) {
            $location = Location::find($this->locationId);
            if ($location) {
                $this->name = $location->name;
                $this->address = $location->address;
                $this->city = $location->city;
                $this->state = $location->state;
                $this->country = $location->country;
                $this->postal_code = $location->postal_code;
                $this->is_active = $location->is_active;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->locationId) {
                $location = Location::find($this->locationId);
                $location->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'country' => $this->country,
                    'postal_code' => $this->postal_code,
                    'is_active' => $this->is_active,
                ]);
                $this->success('Location updated successfully!');
                $this->dispatch('location-updated');
            } else {
                Location::create([
                    'name' => $this->name,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'country' => $this->country,
                    'postal_code' => $this->postal_code,
                    'is_active' => $this->is_active,
                ]);
                $this->success('Location created successfully!');
                $this->dispatch('location-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->address = '';
        $this->city = '';
        $this->state = '';
        $this->country = '';
        $this->postal_code = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.locations.form');
    }
}