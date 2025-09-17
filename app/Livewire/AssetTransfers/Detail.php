<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Models\AssetTransferItem;
use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferPriority;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class Detail extends Component
{
    use Toast;

    public AssetTransfer $transfer;
    public $showEditModal = false;
    public $showStatusModal = false;
    public $newStatus = '';
    public $statusReason = '';

    protected $listeners = [
        'transferUpdated' => '$refresh',
        'statusChanged' => '$refresh'
    ];

    public function mount(AssetTransfer $transfer)
    {
        $this->transfer = $transfer->load([
            'company',
            'fromLocation',
            'toLocation',
            'requestedBy',
            'approvedBy',
            'items.asset.location',
            'items.fromLocation',
            'items.toLocation'
        ]);
    }

    public function openEditModal()
    {
        $this->showEditModal = true;
    }

    public function openStatusModal()
    {
        $this->showStatusModal = true;
        $this->newStatus = $this->transfer->status->value;
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|string',
            'statusReason' => 'nullable|string|max:500'
        ]);

        $this->transfer->update([
            'status' => AssetTransferStatus::from($this->newStatus),
            'notes' => $this->transfer->notes . "\n\n" . now()->format('Y-m-d H:i:s') . " - Status changed to {$this->newStatus}: {$this->statusReason}"
        ]);

        $this->showStatusModal = false;
        $this->statusReason = '';
        $this->success('Status berhasil diperbarui!');
        $this->dispatch('statusChanged');
    }

    public function getStatusOptions()
    {
        return collect(AssetTransferStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => ucfirst(str_replace('_', ' ', $status->value))
            ];
        })->toArray();
    }

    public function getPriorityBadgeClass($priority)
    {
        return match($priority->value) {
            'low' => 'badge-success',
            'medium' => 'badge-warning',
            'high' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status->value) {
            'draft' => 'badge-neutral',
            'pending' => 'badge-warning',
            'approved' => 'badge-info',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    public function render()
    {
        return view('livewire.asset-transfers.detail');
    }
}