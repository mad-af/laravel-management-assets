<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination, WithAlert;

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
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($transferId)
    {
        $this->dispatch('open-edit-drawer', transferId: $transferId);
    }

    public function viewDetail($transferId)
    {
        return $this->redirect(route('asset-transfers.show', $transferId));
    }

    public function delete($transferId)
    {
        try {
            $transfer = AssetTransfer::findOrFail($transferId);
            
            // Check if transfer can be deleted (only draft status)
            if ($transfer->status->value !== 'draft') {
                $this->showErrorAlert('Hanya transfer dengan status draft yang dapat dihapus.', 'Error');
                return;
            }
            
            $transfer->delete();
            
            $this->showSuccessAlert('Transfer aset berhasil dihapus.', 'Berhasil');
            $this->dispatch('transfer-deleted');
            
        } catch (\Exception $e) {
            $this->showErrorAlert('Gagal menghapus transfer aset: ' . $e->getMessage(), 'Error');
        }
    }

    public function render()
    {
        $transfers = AssetTransfer::query()
            ->with(['fromBranch', 'toBranch', 'requestedBy'])
            ->withCount('items')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transfer_no', 'like', '%' . $this->search . '%')
                      ->orWhere('reason', 'like', '%' . $this->search . '%')
                      ->orWhereHas('fromBranch', function ($branch) {
                          $branch->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('toBranch', function ($branch) {
                          $branch->where('name', 'like', '%' . $this->search . '%');
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