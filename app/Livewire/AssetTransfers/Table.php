<?php

namespace App\Livewire\AssetTransfers;

use App\Enums\AssetTransferAction;
use App\Enums\AssetTransferStatus;
use App\Models\AssetTransfer;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithAlert, WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $actionFilter = AssetTransferAction::DELIVERY->value;

    protected $queryString = ['search', 'statusFilter', 'actionFilter'];

    protected $listeners = [
        'transfer-saved' => '$refresh',
        'transfer-updated' => '$refresh',
        'transfer-deleted' => '$refresh',
    ];

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
            $this->showErrorAlert('Gagal menghapus transfer aset: '.$e->getMessage(), 'Error');
        }
    }

    public function openTransferDetail($transferId, $mode = 'detail')
    {
        $this->dispatch('open-transfer-detail', transferId: (string) $transferId, mode: (string) $mode);
    }

    public function getTransferButtonType($transfer)
    {
        if ($this->actionFilter === AssetTransferAction::DELIVERY->value) {
            return 'detail';
        }

        if ($this->actionFilter === AssetTransferAction::CONFIRMATION->value) {
            return $transfer->status === AssetTransferStatus::DELIVERED ? 'detail' : 'confirm';
        }

        return null;
    }

    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        $transfers = AssetTransfer::query()
            ->with(['fromBranch', 'toBranch', 'items.asset'])
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->actionFilter === AssetTransferAction::DELIVERY->value, function ($query) use ($currentBranchId) {
                $query->deliveryAction($currentBranchId);
            })
            ->when($this->actionFilter === AssetTransferAction::CONFIRMATION->value, function ($query) use ($currentBranchId) {
                $query->confirmationAction($currentBranchId);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transfer_no', 'like', "%{$this->search}%")
                        ->orWhere('reason', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hanya hitung transfer dengan status 'shipped'
        $deliveryCount = AssetTransfer::query()
            ->deliveryAction($currentBranchId)
            ->where('status', AssetTransferStatus::SHIPPED->value)
            ->count();
        $confirmationCount = AssetTransfer::query()
            ->confirmationAction($currentBranchId)
            ->where('status', AssetTransferStatus::SHIPPED->value)
            ->count();

        $actionCounts = [
            AssetTransferAction::DELIVERY->value => $deliveryCount,
            AssetTransferAction::CONFIRMATION->value => $confirmationCount,
        ];

        $transferActions = AssetTransferAction::cases();

        return view('livewire.asset-transfers.table', compact('transfers', 'transferActions', 'actionCounts'));
    }
}
