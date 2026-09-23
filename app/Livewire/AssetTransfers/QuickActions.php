<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Enums\AssetTransferStatus;
use App\Traits\WithAlert;
use Livewire\Component;
use Mary\Traits\Toast;

class QuickActions extends Component
{
    use WithAlert, Toast;

    public $quickActionsData;

    public function mount($quickActionsData)
    {
         $this->quickActionsData = $quickActionsData;
     }

     public function openEditModal()
     {
         $this->dispatch('open-edit-drawer', transferId: $this->quickActionsData['id']);
     }

public function updateStatus($status)
    {
        $transfer = AssetTransfer::find($this->quickActionsData['id']);
        
        if (!$transfer) {
            $this->error('Transfer tidak ditemukan!');
            return;
        }

        $statusMap = [
            'shipped' => AssetTransferStatus::SHIPPED,
            'delivered' => AssetTransferStatus::DELIVERED,
        ];

        if (!isset($statusMap[$status])) {
            $this->error("Status '{$status}' tidak dikenali. Status valid: shipped, delivered.");
            return;
        }

        $newStatus = $statusMap[$status];
        $transfer->update(['status' => $newStatus]);
        
        $this->quickActionsData['status'] = $newStatus->value;
        
        $statusMessages = [
            'shipped' => 'Transfer ditandai dikirim!',
            'delivered' => 'Transfer ditandai terkirim!',
        ];
        
        $this->success($statusMessages[$status] ?? 'Status berhasil diperbarui!');
        
        $this->dispatch('transfer-status-updated');
    }



    public function render()
    {
        return view('livewire.asset-transfers.quick-actions');
    }
}