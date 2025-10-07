<?php

namespace App\Livewire\VehicleTaxes;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithAlert, WithPagination;

    public $search = '';

    public $selectedCompanyId = '';

    public $statusFilter = 'due_soon';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCompanyId' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
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

        $query = Asset::with([
            'category',
            'branch',
            'vehicleProfile',
        ])
            ->where('category_id', $vehicleCategory->id);

        // Filter by branch from session
        if (session('selected_branch_id')) {
            $query->where('branch_id', session('selected_branch_id'));
        }

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
        return $this->getBaseVehicleQuery()->count();
    }

    public function getOverdueCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->overdue()->count();
    }

    public function getDueSoonCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->dueSoon()->count();
    }

    public function getPaidCountProperty()
    {
        return $this->getBaseVehicleQuery()
            ->paid()->count();
    }

    public function getNotValidCountProperty()
    {
        return $this->getBaseVehicleQuery()
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

    public function render()
    {
        return view('livewire.vehicle-taxes.table', [
            'vehicleAssets' => $this->getVehicleAssetsProperty(),
            'companies' => $this->getCompaniesProperty(),
        ]);
    }
}
