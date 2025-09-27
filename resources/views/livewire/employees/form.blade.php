<form wire:submit="save" class="space-y-2">

    {{-- Company --}}
    <div>
        <x-select label="Perusahaan" placeholder="Pilih perusahaan" wire:model.live="company_id" :options="$companies"
            option-value="id" option-label="name" class="select-sm" required />
    </div>

    {{-- Branch (opsional, tergantung company) --}}
    <div>
        <x-select label="Cabang" placeholder="Pilih cabang" wire:model="branch_id" :options="$branches"
            option-value="id" option-label="name" class="select-sm" :disabled="!$company_id" />
    </div>

    {{-- Employee Number (nullable + unique) --}}
    <div>
        <x-input label="Nomor Pegawai" wire:model="employee_number" type="text"
            placeholder="Contoh: 1024" class="input-sm" />
    </div>

    {{-- Full Name --}}
    <div>
        <x-input label="Nama Lengkap" wire:model="full_name" placeholder="Masukkan nama lengkap" class="input-sm"
            required />
    </div>

    {{-- Email (nullable) --}}
    <div>
        <x-input label="Email" wire:model="email" type="email" placeholder="nama@domain.com"
            class="input-sm" />
    </div>

    {{-- Phone (nullable) --}}
    <div>
        <x-input label="Nomor HP" wire:model="phone" placeholder="08xxxxxxxxxx" class="input-sm" />
    </div>

    {{-- Status --}}
    <div>
        <x-checkbox label="Aktif" wire:model="is_active" class="text-sm checkbox-sm" />
    </div>

    {{-- Actions --}}
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('close-drawer')" />
        <x-button :label="$isEdit ? 'Update' : 'Simpan'" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>