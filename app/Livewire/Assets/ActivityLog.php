<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;

    public Asset $asset;

    public $showAll = false;

    public array $expanded = [2];

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function toggleShowAll()
    {
        $this->showAll = ! $this->showAll;
        $this->resetPage();
    }

    public function getActionBadgeClass($action)
    {
        $actionColors = [
            'created' => 'badge-success',
            'updated' => 'badge-info',
            'deleted' => 'badge-error',
            'transferred' => 'badge-warning',
            'maintenance' => 'badge-warning',
            'loan' => 'badge-info',
            'return' => 'badge-success',
        ];

        return $actionColors[$action] ?? 'badge-neutral';
    }

    public function getActionLabel($action)
    {
        $actionLabels = [
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            'transferred' => 'Dipindahkan',
            'maintenance' => 'Maintenance',
            'loan' => 'Dipinjam',
            'return' => 'Dikembalikan',
        ];

        return $actionLabels[$action] ?? ucfirst($action);
    }

    public function render()
    {
        $logs = AssetLog::where('asset_id', $this->asset->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        if (! $this->showAll) {
            $logs = $logs->limit(10)->get();
        } else {
            $logs = $logs->paginate(20);
        }

        return view('livewire.assets.activity-log', compact('logs'));
    }
}
