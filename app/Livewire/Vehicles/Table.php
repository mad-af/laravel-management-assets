<?php

namespace App\Livewire\Vehicles;

use App\Models\Asset;
use App\Models\Category;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    public function formatNextServiceDate($nextServiceDate)
    {
        if (! $nextServiceDate) {
            return null;
        }

        try {
            // Pakai locale per instance (tanpa setLocale global)
            $serviceDate = Carbon::parse($nextServiceDate)->locale('id');
            $now = Carbon::now($serviceDate->timezone);

            // Format tanggal dengan nama bulan Indonesia - gunakan 'j M Y' untuk menghindari duplikasi
            $formattedDate = $serviceDate->translatedFormat('j M Y');

            // Same-day
            if ($serviceDate->isSameDay($now)) {
                return [
                    'formatted_date' => $formattedDate,
                    'time_info' => 'Hari ini',
                    'is_overdue' => false,
                    'days_left' => 0,
                ];
            }

            // Buat frasa human-friendly tanpa awalan/akhiran ("2 bulan 3 hari")
            $span = $serviceDate->diffForHumans($now, [
                'parts' => 2,              // ambil 2 unit terbesar (mis: "2 bulan 3 hari")
                'join' => true,           // gabung dengan spasi
                'short' => false,          // pakai bentuk lengkap
                'syntax' => CarbonInterface::DIFF_ABSOLUTE, // tanpa "dalam"/"yang lalu"
            ]);

            $isOverdue = $serviceDate->lessThan($now);
            $timeInfo = $isOverdue ? "{$span} yang lalu" : "{$span} lagi";

            // Selisih hari integer (tanpa pecahan), dinormalisasi ke awal hari
            $daysLeft = $now->startOfDay()->diffInDays($serviceDate->startOfDay(), false);

            return [
                'formatted_date' => $formattedDate, // contoh: "15 Des 2024"
                'time_info' => $timeInfo,      // contoh: "7 hari lagi" / "2 bulan 3 hari yang lalu"
                'is_overdue' => $isOverdue,
                'days_left' => $daysLeft,      // contoh: 7 atau -3
            ];
        } catch (\Throwable $e) {
            return null; // atau lempar exception sesuai kebutuhanmu
        }
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
