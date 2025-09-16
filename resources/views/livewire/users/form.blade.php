<form wire:submit="save" class="space-y-4">
    <!-- User Name -->
    <div>
        <x-input label="Nama Lengkap" wire:model="name" placeholder="Masukkan nama lengkap" required />
    </div>

    <!-- Email -->
    <div>
        <x-input label="Email" wire:model="email" placeholder="Masukkan email" type="email" required />
    </div>

    <!-- Password Fields -->
    @if(!$isEdit)
        <div>
            <x-input label="Password" wire:model="password" placeholder="Masukkan password" type="password" required />
        </div>
        <div>
            <x-input label="Konfirmasi Password" wire:model="password_confirmation" placeholder="Konfirmasi password" type="password" required />
        </div>
    @else
        <div>
            <x-input label="Password Baru (Kosongkan jika tidak ingin mengubah)" wire:model="password" placeholder="Masukkan password baru" type="password" />
        </div>
        <div>
            <x-input label="Konfirmasi Password Baru" wire:model="password_confirmation" placeholder="Konfirmasi password baru" type="password" />
        </div>
    @endif

    <!-- Company Selection -->
    <div>
        <x-select label="Perusahaan" wire:model="company_id" placeholder="Pilih perusahaan" required>
            @foreach($allCompanies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </x-select>
    </div>

    <!-- Role Selection -->
    <div>
        <x-select label="Role" wire:model="role" placeholder="Pilih role" required>
            @foreach($allRoles as $roleOption)
                <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
            @endforeach
        </x-select>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end gap-2 pt-4">
        <x-button label="Batal" class="btn-ghost" wire:click="$dispatch('closeDrawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary" type="submit" spinner="save" />
    </div>
</form>