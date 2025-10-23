<form wire:submit="save" class="space-y-4">
    <!-- Nama Provider -->
    <div>
        <x-input label="Nama Provider" wire:model="name" placeholder="Masukkan nama provider" class="input-sm" required />
    </div>

    <!-- Telepon -->
    <div>
        <x-input label="Telepon" wire:model="phone" placeholder="Masukkan nomor telepon" class="input-sm" />
    </div>

    <!-- Email -->
    <div>
        <x-input label="Email" wire:model="email" type="email" placeholder="Masukkan email" class="input-sm" />
    </div>

    <!-- Alamat -->
    <div>
        <x-textarea label="Alamat" wire:model="address" placeholder="Masukkan alamat" rows="3" class="textarea-sm" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>