<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Enums\AssetTransferStatus;
use App\Traits\WithAlert;
use Livewire\Component;

class QuickActions extends Component
{
    use WithAlert;

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

         $transfer->update(['status' => $status]);
         
         $this->quickActionsData['status'] = $status;
         
         $statusMessages = [
             'approved' => 'Transfer berhasil disetujui!',
             'rejected' => 'Transfer berhasil ditolak!',
             'in_progress' => 'Transfer berhasil dimulai!',
             'completed' => 'Transfer berhasil diselesaikan!'
         ];
         
         $this->success($statusMessages[$status] ?? 'Status berhasil diperbarui!');
         
         $this->dispatch('transfer-status-updated');
     }



    public function render()
    {
        return view('livewire.asset-transfers.quick-actions');
    }
}