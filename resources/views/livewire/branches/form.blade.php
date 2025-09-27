<form wire:submit="save" class="space-y-2">
    <!-- Location Name -->
    <div>
        <x-input label="Nama Cabang" wire:model="name" placeholder="Masukkan nama cabang" required />
    </div>
    
    <!-- Alamat -->
    <div>
        <x-textarea
            label="Alamat"
            wire:model="address"
            placeholder="Masukkan alamat cabang"
            rows="3"
        />
    </div>

    <!-- Status -->
    <div>
        <x-checkbox label="Aktif" wire:model="is_active" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>