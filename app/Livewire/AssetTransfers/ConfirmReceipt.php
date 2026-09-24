<?php

namespace App\Livewire\AssetTransfers;

use App\Enums\AssetTransferStatus;
use App\Models\AssetTransfer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class ConfirmReceipt extends Component
{
    use Toast;

    public string $transferId;

    public string $confirmation_text = '';

    public function mount(string $transferId)
    {
        $this->transferId = $transferId;
    }

    public function confirmReceipt(): void
    {
        if ($this->confirmation_text !== 'Saya telah menerima asset') {
            $this->addError('confirmation_text', 'Ketik tepat: "Saya telah menerima asset" untuk konfirmasi.');

            return;
        }

        $transfer = AssetTransfer::find($this->transferId);
        if (! $transfer) {
            $this->addError('confirmation_text', 'Transfer tidak ditemukan.');

            return;
        }

        if ($transfer->status === AssetTransferStatus::DELIVERED) {
            $this->warning('Transfer ini sudah dikonfirmasi sebelumnya.');
            $this->dispatch('close-drawer');

            return;
        }

        $transfer->update([
            'status' => AssetTransferStatus::DELIVERED,
            'delivery_at' => now(),
            'accepted_by' => Auth::id(),
            'accepted_at' => now(),
        ]);

        $this->dispatch('transfer-updated');
        $this->success('Penerimaan aset berhasil dikonfirmasi!');
        $this->dispatch('close-drawer');
    }

    // Computed: apakah frasa konfirmasi cocok
    public function getIsConfirmedProperty(): bool
    {
        return $this->confirmation_text === 'Saya telah menerima asset';
    }

    public function render()
    {
        return view('livewire.asset-transfers.confirm-receipt');
    }
}
