<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'transfer-saved' => '$refresh',
        'transfer-updated' => '$refresh',
        'transfer-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('openDrawer');
    }

    public function openEditDrawer($transferId)
    {
        $this->dispatch('openEditDrawer', $transferId);
    }

    public function delete($transferId)
    {
        try {
            $transfer = AssetTransfer::find($transferId);
            if ($transfer) {
                $transfer->delete();
                $this->dispatch('transfer-deleted');
            }
        } catch (\Exception $e) {
            // Handle error silently or add toast notification
        }
    }

    public function render()
    {
        $transfers = AssetTransfer::query()
            ->with(['fromLocation', 'toLocation', 'requestedBy'])
            ->withCount('items')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transfer_no', 'like', '%' . $this->search . '%')
                      ->orWhere('reason', 'like', '%' . $this->search . '%')
                      ->orWhereHas('fromLocation', function ($location) {
                          $location->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('toLocation', function ($location) {
                          $location->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->whereRaw('LOWER(status) = ?', [strtolower($this->statusFilter)]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.asset-transfers.table', compact('transfers'));
    }
}