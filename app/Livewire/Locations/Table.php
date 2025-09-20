<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Table extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $listeners = [
        'location-saved' => '$refresh',
        'location-updated' => '$refresh',
        'location-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openEditDrawer($locationId)
    {
        $this->dispatch('open-edit-drawer', locationId: $locationId);
    }

    public function delete($locationId)
    {
        try {
            $location = Location::findOrFail($locationId);
            $location->delete();
            $this->success('Location deleted successfully!');
            $this->dispatch('location-deleted');
        } catch (\Exception $e) {
            $this->error('Failed to delete location: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Location::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('state', 'like', '%' . $this->search . '%')
                  ->orWhere('country', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $locations = $query->orderBy('created_at', 'desc')
                          ->paginate($this->perPage);

        return view('livewire.locations.table', [
            'locations' => $locations,
        ]);
    }
}