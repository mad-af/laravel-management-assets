<?php

namespace App\Livewire\AssetLogs;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\User;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class ForAsset extends Component
{
    use WithPagination, WithAlert;

    public Asset $asset;
    public $actionFilter = '';
    public $userFilter = '';
    public $dateFromFilter = '';
    public $dateToFilter = '';

    protected $queryString = ['actionFilter', 'userFilter', 'dateFromFilter', 'dateToFilter'];

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
        
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

    public function clearFilters()
    {
        $this->actionFilter = '';
        $this->userFilter = '';
        $this->dateFromFilter = '';
        $this->dateToFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = AssetLog::with(['user'])
            ->forAsset($this->asset->id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->actionFilter) {
            $query->byAction($this->actionFilter);
        }

        if ($this->userFilter) {
            $query->byUser($this->userFilter);
        }

        if ($this->dateFromFilter) {
            $query->whereDate('created_at', '>=', $this->dateFromFilter);
        }

        if ($this->dateToFilter) {
            $query->whereDate('created_at', '<=', $this->dateToFilter);
        }

        $logs = $query->paginate(15);

        // Get available actions for filter dropdown
        $actions = AssetLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->map(function($action) {
                return $action instanceof \App\Enums\AssetLogAction ? $action->value : $action;
            });

        // Get users for filter dropdown
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('livewire.asset-logs.for-asset', compact('logs', 'actions', 'users'));
    }
}