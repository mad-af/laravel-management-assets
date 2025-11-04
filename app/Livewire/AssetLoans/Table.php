<?php

namespace App\Livewire\AssetLoans;

use App\Enums\AssetLoanStatus;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Support\SessionKey;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    // Default to show active loans to avoid empty list
    public $statusFilter = AssetLoanStatus::AVAILABLE->value;

    public $conditionFilter = '';

    public $overdueFilter = false;

    public $categoryFilter = '';

    protected $queryString = ['search', 'statusFilter', 'conditionFilter', 'overdueFilter', 'categoryFilter'];

    protected $listeners = [
        'table-refresh' => '$refresh',
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

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function openDrawer($assetId)
    {
        $this->dispatch('open-drawer', assetId: $assetId);
    }

    public function openEditDrawer($assetLoanId)
    {
        $this->dispatch('open-edit-drawer', assetLoanId: $assetLoanId);
    }

    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);
        // Base query for assets
        $assetsQuery = Asset::query()
            ->forBranch($currentBranchId)
            ->with([
                'category',
                'currentLoan.employee',
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
            ->when($this->statusFilter === AssetLoanStatus::AVAILABLE->value, function ($query) {
                $query->available();
            })
            ->when($this->statusFilter === AssetLoanStatus::ON_LOAN->value || $this->statusFilter === 'active', function ($query) {
                $query->where('status', AssetStatus::ON_LOAN);
            })
            ->when($this->statusFilter === AssetLoanStatus::OVERTIME->value, function ($query) {
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
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            });

        // Cache paginated assets list keyed by filters, branch, and page
        $page = request()->query('page', 1);
        $assetsCacheKey = sprintf(
            'asset_loans:assets:list:b%d:s%s:sf%s:cf%s:of%s:cat%s:p%d',
            (int) $currentBranchId,
            md5($this->search ?? ''),
            $this->statusFilter ?? '-',
            $this->conditionFilter ?: '-',
            $this->overdueFilter ? '1' : '0',
            $this->categoryFilter ?: '-',
            (int) $page
        );

        $assets = Cache::remember($assetsCacheKey, 30, function () use ($assetsQuery) {
            return $assetsQuery
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });

        // Build dynamic status counts keyed by enum value
        // Cache status counts per branch for a short TTL
        $availableCount = Cache::remember("asset_loans:count:available:b{$currentBranchId}", 60, function () use ($currentBranchId) {
            return Asset::forBranch($currentBranchId)->available()->count();
        });

        $onLoanCount = Cache::remember("asset_loans:count:onloan:b{$currentBranchId}", 60, function () use ($currentBranchId) {
            return Asset::forBranch($currentBranchId)->where('status', AssetStatus::ON_LOAN)->count();
        });

        $overdueCount = Cache::remember("asset_loans:count:overdue:b{$currentBranchId}", 60, function () use ($currentBranchId) {
            return Asset::forBranch($currentBranchId)
                ->where('status', AssetStatus::ON_LOAN)
                ->whereHas('loans', function ($q) {
                    $q->whereNull('checkin_at')->where('due_at', '<', now());
                })
                ->count();
        });

        $statusCounts = [
            AssetLoanStatus::AVAILABLE->value => $availableCount,
            AssetLoanStatus::ON_LOAN->value => $onLoanCount,
            AssetLoanStatus::OVERTIME->value => $overdueCount,
        ];

        // Provide status cases to the view if needed
        $loanStatuses = AssetLoanStatus::cases();

        // Provide categories to the view (cached)
        $categories = Cache::remember('categories:active:list', 300, function () {
            return Category::active()->orderBy('name')->get();
        });

        return view('livewire.asset-loans.table', compact('assets', 'statusCounts', 'loanStatuses', 'categories'));
    }

    public function returnAsset($assetId, $assetLoanId)
    {
        $this->dispatch('open-return-drawer', assetId: $assetId, assetLoanId: $assetLoanId);
    }
}
