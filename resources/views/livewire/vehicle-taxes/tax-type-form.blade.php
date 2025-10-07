<form wire:submit="save" class="space-y-4">

    {{-- Asset --}}
    <div>
        <x-select label="Asset" placeholder="Pilih asset" wire:model.live="asset_id" :options="$assets" option-value="id"
            option-label="name" class="select-sm" required />
    </div>

    @if ($asset_id)
        
        {{-- Due Date --}}
        <div>
            <fieldset class="p-4 rounded-lg border border-base-300 bg-base-200">
                <legend class="px-2 text-xs font-medium">Pajak Tahunan</legend>
                <x-input label="Tanggal Jatuh Tempo" wire:model="due_date" type="date" class="input-sm" inline required />
            </fieldset>
        </div>

        <div>
            <x-checkbox label="Aktifkan Pajak KIR" wire:model.live="is_kir" hint="Centang untuk mengaktifkan pajak KIR" />
        </div>

        @if ($is_kir)
        <div>
            <fieldset class="p-4 rounded-lg border border-base-300 bg-base-200">
                <legend class="px-2 text-xs font-medium">Pajak KIR</legend>
                <x-input label="Tanggal Jatuh Tempo" wire:model="due_date_kir" type="date" class="input-sm" inline required />
            </fieldset>
        </div>
        @endif


        {{-- Actions --}}
        <div class="flex gap-2 justify-end pt-4">
            <x-button label="Batal" class="btn-sm btn-ghost" wire:click="$dispatch('close-drawer')" />
            <x-button :label="$isEdit ? 'Update' : 'Simpan'" class="btn-sm btn-primary" type="submit" spinner="save" />
        </div>
    @endif
</form>