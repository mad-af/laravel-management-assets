<?php

namespace App\Livewire\AssetLoans;

use App\Models\AssetLoan;
use App\Models\Asset;
use App\Enums\LoanCondition;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $conditionFilter = '';
    public $overdueFilter = false;

    protected $queryString = ['search', 'statusFilter', 'conditionFilter', 'overdueFilter'];

    protected $listeners = [
        'asset-loan-saved' => '$refresh',
        'asset-loan-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingConditionFilter()
    {
        $this->resetPage();
    }

    public function updatingOverdueFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('openDrawer');
    }

    public function openEditDrawer($assetLoanId)
    {
        $this->dispatch('openEditDrawer', $assetLoanId);
    }

    public function render()
    {
        $assetLoans = AssetLoan::query()
            ->with(['asset', 'asset.category', 'asset.location'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('borrower_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('asset', function ($assetQuery) {
                          $assetQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('code', 'like', '%' . $this->search . '%')
                                    ->orWhere('tag_code', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->active();
            })
            ->when($this->statusFilter === 'returned', function ($query) {
                $query->whereNotNull('checkin_at');
            })
            ->when($this->conditionFilter, function ($query) {
                $query->where('condition_out', $this->conditionFilter)
                      ->orWhere('condition_in', $this->conditionFilter);
            })
            ->when($this->overdueFilter, function ($query) {
                $query->overdue();
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $assets = Asset::available()->orderBy('name')->get();
        $conditions = LoanCondition::cases();

        return view('livewire.asset-loans.table', compact('assetLoans', 'assets', 'conditions'));
    }
}