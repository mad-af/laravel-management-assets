<?php

namespace App\Livewire\AssetLoans;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    // Default to show active loans to avoid empty list
    public $statusFilter = 'on_loan';

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
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($assetLoanId)
    {
        $this->dispatch('open-edit-drawer', assetLoanId: $assetLoanId);
    }

    public function render()
    {
        // Base query for assets
        $assetsQuery = Asset::query()
            ->with([
                'category',
                // eager-load active loan and employee to render borrower details
                'loans' => function ($q) {
                    $q->whereNull('checkin_at')
                        ->with('employee')
                        ->orderBy('checkout_at', 'desc');
                },
            ])
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term)
                        ->orWhere('tag_code', 'like', $term)
                        ->orWhereHas('loans.employee', function ($empQuery) use ($term) {
                            $empQuery->where('full_name', 'like', $term);
                        });
                });
            })
            // Map statusFilter driven by tabs to Asset status and loan state
            ->when($this->statusFilter === 'available', function ($query) {
                $query->available();
            })
            ->when($this->statusFilter === 'on_loan' || $this->statusFilter === 'active', function ($query) {
                $query->where('status', AssetStatus::ON_LOAN);
            })
            ->when($this->statusFilter === 'overdue', function ($query) {
                $query->where('status', AssetStatus::ON_LOAN)
                    ->whereHas('loans', function ($loanQuery) {
                        $loanQuery->whereNull('checkin_at')
                            ->where('due_at', '<', now());
                    });
            })
            // Existing dropdown filters still respected
            ->when($this->statusFilter === 'returned', function ($query) {
                // Assets that have been loaned and returned at least once
                $query->available()->whereHas('loans', function ($loanQuery) {
                    $loanQuery->whereNotNull('checkin_at');
                });
            })
            ->when($this->conditionFilter, function ($query) {
                $query->whereHas('loans', function ($loanQuery) {
                    $loanQuery->where('condition_out', $this->conditionFilter)
                        ->orWhere('condition_in', $this->conditionFilter);
                });
            })
            ->when($this->overdueFilter, function ($query) {
                $query->whereHas('loans', function ($loanQuery) {
                    $loanQuery->whereNull('checkin_at')
                        ->where('due_at', '<', now());
                });
            });

        $assets = $assetsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Assets available for new loan (for drawer)
        $availableAssets = Asset::available()->orderBy('name')->get();

        // Counts for tabs
        $availableCount = Asset::available()->count();
        $onLoanCount = Asset::query()->where('status', AssetStatus::ON_LOAN)->count();
        $overdueCount = Asset::query()
            ->where('status', AssetStatus::ON_LOAN)
            ->whereHas('loans', function ($q) {
                $q->whereNull('checkin_at')->where('due_at', '<', now());
            })
            ->count();

        return view('livewire.asset-loans.table', compact('assets', 'availableAssets', 'availableCount', 'onLoanCount', 'overdueCount'));
    }
}
