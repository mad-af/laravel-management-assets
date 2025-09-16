<form wire:submit="save" class="space-y-4">
    <!-- Category Name -->
    <div>
        <x-input label="Nama Kategori" wire:model="name" placeholder="Masukkan nama kategori" required />
    </div>

    <!-- Company Selection -->
    <div>
        <x-select label="Perusahaan" wire:model="company_id" placeholder="Pilih perusahaan" required>
            @foreach($allCompanies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </x-select>
    </div>

    <!-- Status -->
    <div>
        <x-checkbox label="Aktif" wire:model="is_active" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('closeDrawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>