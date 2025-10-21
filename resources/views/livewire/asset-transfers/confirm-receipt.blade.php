<div class="p-3 mt-6 border rounded-box bg-base-200 border-base-300">
    <p class="mb-2 text-sm text-base-content/70">Untuk memastikan Anda benar-benar menerima aset, ketik kalimat berikut:</p>
    <x-input label="Ketik: saya telah menerima asset" wire:model.live.300ms="confirmationInput" class="input-sm" />
    @error('confirmationInput')
        <p class="mt-1 text-xs text-error">{{ $message }}</p>
    @enderror
    <div class="flex justify-end mt-3">
        <button type="button" class="btn btn-sm btn-success" wire:click="confirmReceipt" @disabled(! str($confirmationInput)->lower()->trim()->exactly('saya telah menerima asset'))>
            <x-icon name="o-check-circle" class="w-4 h-4" />
            Konfirmasi Penerimaan
        </button>
    </div>
</div>