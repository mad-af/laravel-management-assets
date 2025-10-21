<?php

namespace App\Livewire\AssetTransfers;

use App\Enums\AssetTransferStatus;
use App\Models\AssetTransfer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConfirmReceipt extends Component
{
    public string $transferId;
    public string $confirmationInput = '';

    public function mount(string $transferId)
    {
        $this->transferId = $transferId;
    }

    public function confirmReceipt(): void
    {
        if (trim(mb_strtolower($this->confirmationInput)) !== 'saya telah menerima asset') {
            $this->addError('confirmationInput', 'Ketik tepat: "saya telah menerima asset" untuk konfirmasi.');
            return;
        }

        $transfer = AssetTransfer::find($this->transferId);
        if (! $transfer) {
            $this->addError('confirmationInput', 'Transfer tidak ditemukan.');
            return;
        }

        $transfer->update([
            'status' => AssetTransferStatus::DELIVERED,
            'delivery_at' => now(),
            'accepted_by' => Auth::id(),
            'accepted_at' => now(),
        ]);

        $this->dispatch('transfer-updated');
    }

    public function render()
    {
        return view('livewire.asset-transfers.confirm-receipt');
    }
}