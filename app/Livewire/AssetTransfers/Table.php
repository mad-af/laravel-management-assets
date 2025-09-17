<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'openAssetTransferDrawer' => 'openDrawer',
        'openEditDrawer' => 'openEditDrawer',
        'transfer-saved' => '$refresh',
        'transfer-updated' => '$refresh'
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
        $this->dispatch('editTransfer', $transferId);
    }

    public function delete($transferId)
    {
        $transfer = AssetTransfer::findOrFail($transferId);
        
        // Only allow deletion of draft transfers
        if ($transfer->status !== 'draft') {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Only draft transfers can be deleted.'
            ]);
            return;
        }

        $transfer->delete();
        
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Asset transfer deleted successfully.'
        ]);
        
        $this->dispatch('asset-transfer-deleted');
    }

    public function render()
    {
        $transfers = AssetTransfer::with(['company', 'requestedBy', 'approvedBy'])
            ->where('company_id', Auth::user()->company_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transfer_no', 'like', '%' . $this->search . '%')
                      ->orWhere('reason', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.asset-transfers.table', compact('transfers'));
    }
}