<form wire:submit="save" class="space-y-2">
    {{-- Company --}}
    <div>
        <x-select label="Perusahaan" placeholder="Pilih perusahaan" wire:model.live="company_id" :options="$companies"
            option-value="id" option-label="name" class="select-sm" required :disabled="$isEdit" />
    </div>

    <!-- Location Name -->
    <div>
        <x-input class="input-sm" label="Nama Cabang" wire:model="name" placeholder="Masukkan nama cabang" required />
    </div>
    
    <!-- Alamat -->
    <div>
        <x-textarea
            class="textarea-sm"
            label="Alamat"
            wire:model="address"
            placeholder="Masukkan alamat cabang"
            rows="3"
        />
    </div>

    <!-- Status -->
    <div>
        <x-checkbox label="Aktif" wire:model="is_active" class="checkbox-sm" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save" />
    </div>
</form>