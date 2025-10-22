<form wire:submit="save" class="space-y-4">

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
        
        {{-- Due Date --}}
        <div>
            <x-checkbox label="Aktifkan Pajak Tahunan" wire:model.live="is_pajak_tahunan" hint="Centang untuk mengaktifkan pajak tahunan" />
        </div>
        
        @if($is_pajak_tahunan)
        <div>
            <fieldset class="p-4 rounded-lg border border-base-300 bg-base-200">
                <legend class="px-2 text-xs font-medium">Pajak Tahunan</legend>
                <livewire:components.day-month-picker label="Tanggal Jatuh Tempo" wire:model="due_date" required />
            </fieldset>
        </div>
        @endif

        <div>
            <x-checkbox label="Aktifkan Pajak KIR" wire:model.live="is_kir" hint="Centang untuk mengaktifkan pajak KIR" />
        </div>

        @if ($is_kir)
        <div>
            <fieldset class="p-4 rounded-lg border border-base-300 bg-base-200">
                <legend class="px-2 text-xs font-medium">Pajak KIR</legend>
                <livewire:components.day-month-picker label="Tanggal Jatuh Tempo" wire:model="due_date_kir" required />
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