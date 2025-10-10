<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithAlert, WithPagination;

    public $search = '';

    public $selectedCompanyId = '';

    public array $expanded = [2];

    public $statusFilter = 'due_soon';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCompanyId' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public $currentBranchId = '';

    public function mount()
    {
        $this->currentBranchId = session_get(SessionKey::BranchId);
        // Set default company if user has access to only one
        if (empty($this->selectedCompanyId)) {
            $companies = $this->getCompaniesProperty();
            if ($companies->count() === 1) {
                $this->selectedCompanyId = $companies->first()->id;
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCompanyId()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getCompaniesProperty()
    {
        return Company::orderBy('name')->get();
    }

    public function getVehicleAssetsProperty()
    {
        // Get vehicle category
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();

        if (! $vehicleCategory) {
            return collect();
        }

        $query = Asset::forBranch($this->currentBranchId)
            ->with([
                'category',
                'branch',
                'vehicleProfile',
                'vehicleTaxTypes',
                'vehicleTaxHistories',
            ])
            ->where('category_id', $vehicleCategory->id);

        // Search functionality
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('tag_code', 'like', '%'.$this->search.'%')
                    ->orWhereHas('vehicleProfile', function ($vq) {
                        $vq->where('plate_no', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Status filter based on tax due dates using VehicleProfile scopes
        switch ($this->statusFilter) {
            case 'overdue':
                $query->overdue();
                break;

            case 'due_soon':
                $query->dueSoon();
                break;

            case 'paid':
                $query->paid();
                break;

            case 'not_valid':
                $query->notValid();
                break;
        }

        return $query->orderBy('name')->paginate(10);
    }

    public function getTotalVehiclesProperty()
    {
        return $this->getBaseVehicleQuery()->forBranch($this->currentBranchId)->count();
    }

    public function getOverdueCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->forBranch($this->currentBranchId)
            ->overdue()->count();
    }

    public function getDueSoonCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->forBranch($this->currentBranchId)
            ->dueSoon()->count();
    }

    public function getPaidCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->forBranch($this->currentBranchId)
            ->paid()->count();
    }

    public function getNotValidCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->forBranch($this->currentBranchId)
            ->notValid()->count();
    }

    private function getBaseVehicleQuery()
    {
        // Get vehicle category
        $vehicleCategory = Category::where('name', 'Kendaraan')->first();

        if (! $vehicleCategory) {
            return Asset::whereRaw('1 = 0'); // Return empty query
        }

        $query = Asset::where('category_id', $vehicleCategory->id);

        // Filter by company
        if ($this->selectedCompanyId) {
            $query->where('company_id', $this->selectedCompanyId);
        }

        // Search functionality
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('tag_code', 'like', '%'.$this->search.'%')
                    ->orWhereHas('vehicleProfile', function ($vq) {
                        $vq->where('plate_no', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return $query;
    }

    public function openEditDrawer($assetId)
    {
        $this->dispatch('open-vehicle-tax-drawer', ['assetId' => $assetId]);
    }

    /**
     * Get status tabs configuration
     */
    public function getStatusTabs()
    {
        return [
            [
                'value' => 'overdue',
                'label' => 'Terlambat',
                'count' => $this->overdueCount,
                'badge_class' => 'badge-sm badge-error',
            ],
            [
                'value' => 'due_soon',
                'label' => 'Jatuh Tempo',
                'count' => $this->dueSoonCount,
                'badge_class' => 'badge-sm badge-warning',
            ],
            [
                'value' => 'paid',
                'label' => 'Dibayar',
                'count' => $this->paidCount,
                'badge_class' => 'badge-sm badge-success',
            ],
            [
                'value' => 'not_valid',
                'label' => 'Belum Valid',
                'count' => $this->notValidCount,
                'badge_class' => 'badge-sm badge-ghost badge-soft',
            ],
        ];
    }

    /**
     * Get table headers configuration
     */
    public function getTableHeaders()
    {
        return [
            ['key' => 'vehicle_info', 'label' => 'Kendaraan'],
            ['key' => 'plate_no', 'label' => 'Plat Nomor'],
            ['key' => 'last_tax_types', 'label' => 'Jenis Pajak'],
            ['key' => 'last_payment', 'label' => 'Pembayaran Terakhir'],
            ['key' => 'payment_count', 'label' => 'Jumlah Pembayaran'],
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-24'],
        ];
    }

    /**
     * Get last payment for a vehicle
     */
    public function getLastPayment($vehicle)
    {
        return $vehicle->vehicleTaxHistories->sortByDesc('due_date')->first();
    }

    /**
     * Get payment count data for a vehicle
     */
    public function getPaymentCount($vehicle)
    {
        $paidCount = $vehicle->vehicleTaxHistories->count();
        $totalTaxTypes = $vehicle->vehicleTaxTypes->count();

        return [
            'paid_count' => $paidCount,
            'total_tax_types' => $totalTaxTypes,
        ];
    }

    /**
     * Calculate tax status for a given vehicle tax type
     */
    public function getTaxStatus($taxType)
    {
        $dueDate = \Carbon\Carbon::parse($taxType->due_date);
        $paidHistory = $taxType->asset->vehicleTaxHistories->where('vehicle_tax_type_id', $taxType->id)->first();

        if ($paidHistory && $paidHistory->paid_date) {
            return [
                'status' => 'paid',
                'statusClass' => 'badge-success',
                'statusText' => 'Dibayar',
            ];
        } elseif ($dueDate->isPast()) {
            return [
                'status' => 'overdue',
                'statusClass' => 'badge-error',
                'statusText' => 'Terlambat',
            ];
        } elseif ($dueDate->isFuture()) {
            return [
                'status' => 'due_soon',
                'statusClass' => 'badge-warning',
                'statusText' => 'Jatuh Tempo',
            ];
        } else {
            return [
                'status' => 'upcoming',
                'statusClass' => 'badge-info',
                'statusText' => 'Akan Datang',
            ];
        }
    }

    /**
     * Sort vehicle tax histories based on status filter
     */
    public function getSortedTaxHistories($vehicle)
    {
        // Ambil koleksi tax histories
        $taxHistories = $vehicle->vehicleTaxHistories;

        if (in_array($this->statusFilter, ['due_soon', 'overdue'])) {
            // Skenario 1: due_soon/overdue - Filter yang belum bayar, kemudian urutkan berdasarkan due_date ASCENDING
            $taxHistories = $taxHistories->filter(fn ($tax) => is_null($tax->paid_date))->sortBy('due_date');
        } else {
            // Skenario 2: Status Lainnya - Urutkan:
            // 1. Yang belum bayar (unpaid) didahulukan
            // 2. Kemudian urutkan berdasarkan due_date DESCENDING
            $taxHistories = $taxHistories->sortBy([
                // Urutan pertama: Berdasarkan status bayar (Unpaid=0, Paid=1) -> Unpaid didahulukan
                fn ($a) => is_null($a->paid_date) ? 0 : 1,

                // Urutan kedua: Berdasarkan due_date DESCENDING
                // Kita balik perbandingan $b <=> $a untuk mendapatkan urutan menurun (DESC)
                fn ($a, $b) => $b->due_date <=> $a->due_date,
            ]);
        }

        return $taxHistories;
    }

    public function render()
    {

        return view('livewire.vehicle-taxes.table', [
            'vehicleAssets' => $this->getVehicleAssetsProperty(),
            'companies' => $this->getCompaniesProperty(),
        ]);
    }
}
