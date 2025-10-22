<div class="p-3 mt-6 border rounded-box bg-base-200 border-base-300">
    <p class="mb-2 text-sm text-base-content/70">Untuk memastikan Anda benar-benar menerima aset.</p>

    <livewire:components.confirmation-phrase wire:model.live="confirmation_text" phrase="Saya telah menerima asset" />
    @error('confirmation_text')
        <p class="mt-1 text-xs text-error">{{ $message }}</p>
    @enderror

    <div class="flex justify-end mt-3">
        <button type="button" class="btn btn-sm btn-success" wire:click="confirmReceipt" @disabled(!$this->isConfirmed)>
            <x-icon name="o-check-circle" class="w-4 h-4" />
            Konfirmasi Penerimaan
        </button>
    </div>
</div>