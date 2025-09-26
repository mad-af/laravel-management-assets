<form wire:submit="save" class="space-y-2">
    <!-- User Name -->
    <div>
        <x-input label="Nama Lengkap" wire:model="name" placeholder="Masukkan nama lengkap" class="input-sm" required />
    </div>

    <!-- Email -->
    <div>
        <x-input label="Email" wire:model="email" placeholder="Masukkan email" type="email" class="input-sm" required />
    </div>

    <!-- Company Selection -->
    <div>
        <x-choices label="Perusahaan" wire:model="company_ids" :options="$companies" option-value="id"
            placeholder="Cari perusahaan..." search-function="searchCompanies" debounce="500ms"
            no-result-text="Tidak ada perusahaan ditemukan" class="input-sm" searchable clearable>
            {{-- Item slot --}}
            @scope('item', $userCompany)
            <x-list-item :item="$userCompany" value="code" sub-value="name">
                <x-slot:avatar>
                    <x-avatar placeholder="{{ strtoupper(substr($userCompany['name'], 0, 2)) }}"
                        class="!w-8 !rounded-lg !bg-primary !font-bold border-2 border-base-100" />
                </x-slot:avatar>
                <x-slot:value>
                    <span class="text-sm">{{ $userCompany['code'] }}</span>
                </x-slot:value>
                <x-slot:sub-value>
                    <span class="text-xs">{{ $userCompany['name'] }}</span>
                </x-slot:sub-value>
            </x-list-item>
            @endscope

            {{-- Selection slot--}}
            @scope('selection', $userCompany)
            {{ $userCompany['name'] }} ({{ $userCompany['code'] }})
            @endscope
        </x-choices>
    </div>

    <!-- Role Selection -->
    <div>
        <x-select label="Role" wire:model="role" :options="$allRoles" option-value="value" option-label="label"
            placeholder="Pilih role" class="select-sm" required
            :disabled="$role === \App\Enums\UserRole::ADMIN->value" />
    </div>

    <!-- Submit Button -->
    <div class="flex gap-2 justify-end pt-4">
        <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$dispatch('close-drawer')" />
        <x-button label="{{ $isEdit ? 'Update' : 'Simpan' }}" class="btn-primary btn-sm" type="submit" spinner="save" />
    </div>
</form>