<?php

namespace App\Livewire\AssetLogs;

use App\Models\AssetLog;
use App\Models\Asset;
use App\Models\User;
use App\Enums\AssetLogAction;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination, WithAlert;

    public $search = '';
    public $assetFilter = '';
    public $actionFilter = '';
    public $userFilter = '';
    public $dateFromFilter = '';
    public $dateToFilter = '';

    protected $queryString = ['search', 'assetFilter', 'actionFilter', 'userFilter', 'dateFromFilter', 'dateToFilter'];

    protected $listeners = [
        'asset-log-created' => '$refresh',
    ];

    public function mount()
    {
        // Handle session flash alerts
        $this->boot();
    }

    public function boot()
    {
        if (session('success')) {
            $this->showSuccessAlert(session('success'));
        }

        if (session('error')) {
            $this->showErrorAlert(session('error'));
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAssetFilter()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function updatingUserFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFromFilter()
    {
        $this->resetPage();
    }

    public function updatingDateToFilter()
    {
        $this->resetPage();
    }

    public function exportLogs()
    {
        // Redirect to export route with current filters
        $params = array_filter([
            'asset_id' => $this->assetFilter,
            'action' => $this->actionFilter,
            'user_id' => $this->userFilter,
            'date_from' => $this->dateFromFilter,
            'date_to' => $this->dateToFilter,
        ]);

        return redirect()->route('asset-logs.export', $params);
    }

    public function render()
    {
        $logs = AssetLog::query()
            ->with(['asset', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('asset', function ($assetQuery) {
                        $assetQuery->where('name', 'like', '%' . $this->search . '%')
                                  ->orWhere('code', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->assetFilter, function ($query) {
                $query->forAsset($this->assetFilter);
            })
            ->when($this->actionFilter, function ($query) {
                $query->byAction($this->actionFilter);
            })
            ->when($this->userFilter, function ($query) {
                $query->byUser($this->userFilter);
            })
            ->when($this->dateFromFilter, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFromFilter);
            })
            ->when($this->dateToFilter, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateToFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $assets = Asset::orderBy('name')->get(['id', 'name', 'code']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = AssetLogAction::cases();

        return view('livewire.asset-logs.table', compact('logs', 'assets', 'users', 'actions'));
    }
}