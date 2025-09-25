<form wire:submit="save" class="space-y-4">
    <!-- Location Name -->
    <div>
        <x-input label="Nama Lokasi" wire:model="name" placeholder="Masukkan nama lokasi" required />
    </div>

    <!-- Status -->
    <div>
        <x-checkbox label="Aktif" wire:model="is_active" />
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end gap-2 pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>