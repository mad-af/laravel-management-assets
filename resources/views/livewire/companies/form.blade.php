<form wire:submit="save" class="space-y-2">
    <!-- Unggah Logo -->
    <x-file label="Logo Perusahaan" wire:model="image" accept="image/png, image/jpeg">
        @if ($image)
            <x-avatar :image="$image" class="!w-16 !rounded-lg !bg-primary !font-bold border-2 border-base-100" />
        @else 
            <div class="flex flex-col justify-center items-center w-16 h-16 rounded-lg bg-base-200 text-base-content/60">
                <x-icon name="o-cloud-arrow-up" class="w-8 h-8" />
                <span>Unggah</span>
            </div>
        @endif
    </x-file>

    <!-- Nama Perusahaan -->
    <x-input wire:model="name" placeholder="Masukkan nama perusahaan" required class="input-sm">
        <x-slot:label>
            <span class="text-xs font-bold label-text text-base-content">Nama Perusahaan</span>
        </x-slot:label>
    </x-input>

    <!-- Kode Perusahaan (unik) -->
    <x-input label="Kode Perusahaan" wire:model="code" placeholder="Masukkan kode perusahaan (maks. 4 karakter)" maxlength="4" required class="input-sm">
        <x-slot:append>
            <x-button label="Generate" wire:click="generateCode" class="join-item btn-sm" />
        </x-slot:append>
    </x-input>

    <!-- Kantor Induk (HQ) - opsional -->
    <x-select
        label="Kantor Induk (HQ)"
        wire:model="hq_branch_id"
        :options="$branches"
        option-value="id"
        option-label="name"
        placeholder="Pilih kantor induk"
        class="select-sm"
    />

    <!-- NPWP / Tax ID -->
    <x-input label="NPWP / Tax ID" wire:model="tax_id" placeholder="Masukkan NPWP / Tax ID" class="input-sm" />

    <!-- Telepon -->
    <x-input label="Telepon" wire:model="phone" placeholder="Masukkan nomor telepon" type="tel" class="input-sm" />

    <!-- Email -->
    <x-input label="Email" wire:model="email" placeholder="Masukkan alamat email" type="email" class="input-sm" />

    <!-- Website -->
    <x-input label="Website" wire:model="website" placeholder="https://contoh.com" type="url" class="input-sm" />

    <!-- Status Aktif -->
    <x-checkbox label="Aktif" wire:model="is_active" class="text-sm checkbox-sm" />

    <!-- Tombol Aksi -->
    <div class="flex gap-2 pt-4">
        <x-button type="button" class="flex-1 btn-sm"
            wire:click="{{ $isEdit ? '$dispatch(\'closeEditDrawer\')' : '$dispatch(\'closeDrawer\')' }}">
            Batal
        </x-button>

        <x-button type="submit" class="flex-1 btn-primary btn-sm" spinner="save">
            <x-icon name="o-check" class="mr-2 w-4 h-4" />
            {{ $isEdit ? 'Update' : 'Simpan ' }}
        </x-button>
    </div>
</form>
