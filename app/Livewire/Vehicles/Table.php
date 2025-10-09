<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use App\Models\Category;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithAlert, WithPagination;

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
            $this->showErrorAlert('Gagal menghapus kendaraan: '.$e->getMessage(), 'Error');
        }
    }

    /**
     * Format next service date dengan informasi waktu yang tersisa
     */
    public function formatNextServiceDate($nextServiceDate)
    {
        if (! $nextServiceDate) {
            return null;
        }

        Carbon::setLocale('id');
        $now = Carbon::now();
        $serviceDate = Carbon::parse($nextServiceDate);

        $diffInDays = $now->diffInDays($serviceDate, false);
        $isOverdue = $diffInDays < 0;
        $absDiffInDays = abs($diffInDays);

        // Format tanggal
        $formattedDate = $serviceDate->format('d M Y');

        // Hitung informasi waktu
        if ($absDiffInDays >= 30) {
            $diffInMonths = $now->diffInMonths($serviceDate, false);
            $absMonths = abs($diffInMonths);
            $timeInfo = $isOverdue
                ? "{$absMonths} bulan yang lalu"
                : "{$absMonths} bulan lagi";
        } else {
            if ($absDiffInDays == 0) {
                $timeInfo = 'Hari ini';
            } else {
                $timeInfo = $isOverdue
                    ? "{$absDiffInDays} hari yang lalu"
                    : "{$absDiffInDays} hari lagi";
            }
        }

        return [
            'formatted_date' => $formattedDate,
            'time_info' => $timeInfo,
            'is_overdue' => $isOverdue,
        ];
    }

    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        $vehicleCategory = Category::where('name', 'Kendaraan')->first();

        $vehicles = Asset::query()
            ->with(['category', 'branch', 'vehicleProfile'])
            ->when($currentBranchId, function ($query) use ($currentBranchId) {
                $query->where('branch_id', $currentBranchId);
            })
            ->where('category_id', $vehicleCategory?->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhereHas('vehicleProfile', function ($profile) {
                            $profile->where('plate_no', 'like', '%'.$this->search.'%')
                                ->orWhere('brand', 'like', '%'.$this->search.'%')
                                ->orWhere('model', 'like', '%'.$this->search.'%')
                                ->orWhere('vin', 'like', '%'.$this->search.'%');
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
