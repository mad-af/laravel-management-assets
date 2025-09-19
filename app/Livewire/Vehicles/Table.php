<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use App\Models\Category;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination, WithAlert;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'vehicle-saved' => '$refresh',
        'vehicle-updated' => '$refresh',
        'vehicle-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openOdometerDrawer($assetId)
    {
        $this->dispatch('open-odometer-drawer', $assetId);
    }

    public function openProfileDrawer($assetId)
    {
        $this->dispatch('open-profile-drawer', $assetId);
    }

    public function viewDetail($vehicleId)
    {
        return $this->redirect(route('vehicles.show', $vehicleId));
    }

    public function delete($vehicleId)
    {
        try {
            $vehicle = Asset::findOrFail($vehicleId);
            
            $vehicle->delete();
            
            $this->showSuccessAlert('Kendaraan berhasil dihapus.', 'Berhasil');
            $this->dispatch('vehicle-deleted');
            
        } catch (\Exception $e) {
            $this->showErrorAlert('Gagal menghapus kendaraan: ' . $e->getMessage(), 'Error');
        }
    }

    public function render()
    {
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();
        
        $vehicles = Asset::query()
            ->with(['category', 'location', 'vehicleProfile'])
            ->where('category_id', $vehicleCategory?->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('vehicleProfile', function ($profile) {
                          $profile->where('plate_no', 'like', '%' . $this->search . '%')
                                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                                  ->orWhere('model', 'like', '%' . $this->search . '%')
                                  ->orWhere('vin', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.vehicles.table', compact('vehicles'));
    }
}