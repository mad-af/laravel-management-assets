<form wire:submit="save" class="space-y-2">

    {{-- Asset --}}
    <div>
        <livewire:components.combobox
            name="assets"
            wire:model.live="asset_id"
            :options="$assets"
            option-value="id"
            option-label="name"
            option-sub-label="tag_code"
            option-meta="code"
            option-avatar="image"
            label="Kendaraan"
            placeholder="Pilih kendaraan"
            required
            class="input-sm" />
    </div>

    @if ($asset_id)
        {{-- Vehicle Tax Need Payment --}}
        <div>
            <x-select label="Pajak yang Harus Dibayar" placeholder="Pilih pajak" wire:model.live="vehicle_tax_history_id"
                :options="$vehicleTaxHistories" option-value="id" option-label="tax_type" class="select-sm" required />
        </div>
    @endif

    @if($vehicle_tax_history_id)
        {{-- Year --}}
        <div>
            <x-input label="Tahun" wire:model="year" type="number" min="2000" max="2099" placeholder="2024" class="input-sm"
                required readonly />
        </div>

        {{-- Paid Date --}}
        <div>
            <x-input label="Tanggal Pembayaran" wire:model="paid_date" type="date" class="input-sm" required />
        </div>

        {{-- Amount --}}
        <div>
            <x-input label="Jumlah Pajak (Rp)" prefix="Rp" wire:model="amount" placeholder="Masukkan jumlah pajak"
                type="number" step="0.01" min="0" class="input-sm" required/>
        </div>

        {{-- Receipt No --}}
        <div>
            <x-input label="Nomor Kwitansi" wire:model="receipt_no" placeholder="Masukkan nomor kwitansi"
                class="input-sm" />
        </div>

        {{-- Notes --}}
        <div>
            <x-textarea label="Catatan" wire:model="notes" placeholder="Catatan tambahan..." class="textarea-sm" rows="3" />
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 justify-end pt-4">
            <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
            <x-button :label="$isEdit ? 'Update' : 'Simpan'" class="btn-primary" type="submit" spinner="save" />
        </div>
    @endif
</form>