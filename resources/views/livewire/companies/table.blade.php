<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari perusahaan..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            {{-- Filter Dropdown --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm btn-outline">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Aktif" wire:click="$set('statusFilter', 'active')" />
                    <x-menu-item title="Tidak Aktif" wire:click="$set('statusFilter', 'inactive')" />
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Perusahaan', 'class' => 'w-56'],
                    ['key' => 'contact', 'label' => 'Kontak'],
                    ['key' => 'location', 'label' => 'Location'],
                    ['key' => 'address', 'label' => 'Alamat'],
                    ['key' => 'users_count', 'label' => 'Users'],
                    ['key' => 'assets_count', 'label' => 'Assets'],
                    ['key' => 'is_active', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$companies" striped show-empty-text>
                @scope('cell_name', $company)
                <div class="flex gap-3 items-center">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                            class="object-cover w-10 h-10 rounded-lg">
                    @else
                        <x-avatar placeholder="{{ strtoupper(substr($company->name, 0, 2)) }}"
                            class="!w-10 !rounded-lg !bg-primary !font-bold" />
                    @endif
                    <div>
                        <div class="font-medium">{{ $company->name }}</div>
                        <div class="text-sm text-base-content/70">{{ $company->code }}</div>
                    </div>
                </div>
                @endscope

                @scope('cell_contact', $company)
                <div>
                    @if($company->email)
                        <div class="text-sm">{{ $company->email }}</div>
                    @endif
                    @if($company->phone)
                        <div class="text-sm text-base-content/70">{{ $company->phone }}</div>
                    @endif
                    @if(!$company->email && !$company->phone)
                        <span class="text-base-content/50">-</span>
                    @endif
                </div>
                @endscope

                @scope('cell_location', $company)
                @if($company->location)
                    <div class="text-sm">{{ $company->location->name }}</div>
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                @scope('cell_address', $company)
                @if($company->address)
                    <div class="max-w-xs text-sm" title="{{ $company->address }}">{{ $company->address }}</div>
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                @scope('cell_users_count', $company)
                {{ $company->users_count ?? 0 }}
                @endscope

                @scope('cell_assets_count', $company)
                {{ $company->assets_count ?? 0 }}
                @endscope

                @scope('cell_is_active', $company)
                @if($company->is_active)
                    <x-badge value="Aktif" class="badge-success badge-sm" />
                @else
                    <x-badge value="Tidak Aktif" class="badge-error badge-sm" />
                @endif
                @endscope

                @scope('cell_created_at', $company)
                {{ $company->created_at->format('d M Y') }}
                @endscope

                @scope('cell_actions', $company)
                <x-action-dropdown :model="$company">
                    <li>
                        <button wire:click="openEditDrawer('{{ $company->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $company->id }}')"
                            wire:confirm="Are you sure you want to delete this company?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($companies->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $companies->firstItem() }}-{{ $companies->lastItem() }} dari {{ $companies->total() }}
                    perusahaan
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $companies->links() }}
                </div>
            </div>
        @endif
    </div>
</div>