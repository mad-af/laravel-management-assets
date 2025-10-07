<form wire:submit="save" class="space-y-2">

    {{-- Asset --}}
    <div>
        <x-select label="Asset" placeholder="Pilih asset" wire:model="asset_id" :options="$assets"
            option-value="id" option-label="name" class="select-sm" required />
    </div>

    {{-- Tax Type --}}
    <div>
        <x-input label="Jenis Pajak" wire:model="tax_type" placeholder="Masukkan jenis pajak (contoh: Pajak Tahunan, STNK)"
            class="input-sm" required />
    </div>

    {{-- Due Date --}}
    <div>
        <x-input label="Tanggal Jatuh Tempo" wire:model="due_date" type="date" class="input-sm" required />
    </div>

    {{-- Actions --}}
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button :label="$isEdit ? 'Update' : 'Simpan'" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>