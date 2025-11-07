<form wire:submit="save" class="space-y-2">
    <!-- User Name -->
    <div>
        <x-input label="Nama Lengkap" wire:model="name" placeholder="Masukkan nama lengkap" class="input-sm" required />
    </div>

    <!-- Email -->
    <div>
        <x-input label="Email" wire:model="email" placeholder="Masukkan email" type="email" class="input-sm" required
            :disabled="$this->isEdit" />
    </div>

    <!-- Company Selection -->
    <div>
        <x-choices label="Perusahaan" wire:model.live="company_ids" :options="$companies" option-value="id"
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
    <fieldset class="p-4 border fieldset bg-base-200 border-base-300 rounded-box w-xs">
        <legend class="fieldset-legend">Cabang</legend>

        @if(empty($company_ids))
            <div class="text-sm text-base-content/60">Pilih perusahaan terlebih dahulu.</div>
        @else
            @foreach($companyBranches as $company)
                <div class="mb-3">
                    <div class="text-sm font-semibold">{{ $company->name }} ({{ $company->code }})</div>
                    <ul class="mt-2 space-y-1">
                        @forelse($company->branches as $branch)
                            <li class="flex gap-2 items-center rounded hover:bg-base-200">
                                <input type="checkbox" class="checkbox checkbox-xs" wire:model="branch_ids" value="{{ $branch->id }}">
                                <span class="text-xs">{{ $branch->name }}</span>
                                @if($branch->is_hq)
                                    <x-badge value="Cabang Utama" class="ml-auto badge-success badge-xs" />
                                @endif
                            </li>
                        @empty
                            <li class="p-2 text-xs text-base-content/50">Tidak ada cabang untuk perusahaan ini.</li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        @endif
    </fieldset>

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