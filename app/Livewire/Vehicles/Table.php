<?php

namespace App\Livewire\Vehicles;

use App\Models\VehicleProfile;
use App\Models\Asset;
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

    public function openDrawer()
    {
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($vehicleId)
    {
        $this->dispatch('open-edit-drawer', vehicleId: $vehicleId);
    }

    public function viewDetail($vehicleId)
    {
        return $this->redirect(route('vehicles.show', $vehicleId));
    }

    public function delete($vehicleId)
    {
        try {
            $vehicle = VehicleProfile::findOrFail($vehicleId);
            
            $vehicle->delete();
            
            $this->showSuccessAlert('Profil kendaraan berhasil dihapus.', 'Berhasil');
            $this->dispatch('vehicle-deleted');
            
        } catch (\Exception $e) {
            $this->showErrorAlert('Gagal menghapus profil kendaraan: ' . $e->getMessage(), 'Error');
        }
    }

    public function render()
    {
        $vehicles = VehicleProfile::query()
            ->with(['asset'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('plate_no', 'like', '%' . $this->search . '%')
                      ->orWhere('brand', 'like', '%' . $this->search . '%')
                      ->orWhere('model', 'like', '%' . $this->search . '%')
                      ->orWhere('vin', 'like', '%' . $this->search . '%')
                      ->orWhereHas('asset', function ($asset) {
                          $asset->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('asset_code', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.vehicles.table', compact('vehicles'));
    }
}