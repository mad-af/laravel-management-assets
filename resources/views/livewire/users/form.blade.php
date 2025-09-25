<form wire:submit="save" class="space-y-4">
    <!-- User Name -->
    <div>
        <x-input label="Nama Lengkap" wire:model="name" placeholder="Masukkan nama lengkap" required />
    </div>

    <!-- Email -->
    <div>
        <x-input label="Email" wire:model="email" placeholder="Masukkan email" type="email" required />
    </div>

    <!-- Company Selection -->
    <div>
        <x-select label="Perusahaan (Opsional)" wire:model="company_id" :options="$allCompanies"
            placeholder="Pilih perusahaan" />
    </div>

    <!-- Role Selection -->
    <div>
        <x-select label="Role" wire:model="role" :options="$allRoles" option-value="value" option-label="label"
            placeholder="Pilih role" required />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>