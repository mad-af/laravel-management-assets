<form wire:submit="save" class="space-y-2">

    {{-- Asset --}}
    <div>
        <x-select label="Asset" placeholder="Pilih asset" wire:model="asset_id" :options="$assets"
            option-value="id" option-label="name" class="select-sm" required />
    </div>

    {{-- Tax Period Start --}}
    <div>
        <x-input label="Periode Pajak Mulai" wire:model="tax_period_start" type="date" class="input-sm" required />
    </div>

    {{-- Tax Period End --}}
    <div>
        <x-input label="Periode Pajak Berakhir" wire:model="tax_period_end" type="date" class="input-sm" required />
    </div>

    {{-- Due Date --}}
    <div>
        <x-input label="Tanggal Jatuh Tempo" wire:model="due_date" type="date" class="input-sm" required />
    </div>

    {{-- Payment Date --}}
    <div>
        <x-input label="Tanggal Pembayaran" wire:model="payment_date" type="date" class="input-sm" />
    </div>

    {{-- Amount --}}
    <div>
        <x-input label="Jumlah" wire:model="amount" type="number" step="0.01" min="0"
            placeholder="0.00" class="input-sm" required />
    </div>

    {{-- Receipt No --}}
    <div>
        <x-input label="Nomor Kwitansi" wire:model="receipt_no" placeholder="Masukkan nomor kwitansi"
            class="input-sm" />
    </div>

    {{-- Notes --}}
    <div>
        <x-textarea label="Catatan" wire:model="notes" placeholder="Catatan tambahan..."
            class="textarea-sm" rows="3" />
    </div>

    {{-- Actions --}}
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button :label="$isEdit ? 'Update' : 'Simpan'" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>