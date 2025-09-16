<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari pengguna..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            {{-- Filter Dropdowns --}}
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

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-user-group" class="btn-sm btn-outline">
                            Filter Role
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Role" wire:click="$set('roleFilter', '')" />
                    @foreach(\App\Enums\UserRole::cases() as $role)
                        <x-menu-item title="{{ $role->label() }}" wire:click="$set('roleFilter', '{{ $role->value }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Nama'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'company', 'label' => 'Perusahaan'],
                    ['key' => 'role', 'label' => 'Role'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$users" striped show-empty-text>
                @scope('cell_name', $user)
                <span class="font-medium">{{ $user->name }}</span>
                @endscope

                @scope('cell_email', $user)
                <div class="text-sm">{{ $user->email }}</div>
                @endscope

                @scope('cell_company', $user)
                @if($user->company)
                    <div class="text-sm">{{ $user->company->name }}</div>
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                @scope('cell_role', $user)
                <x-badge value="{{ $user->role->label() }}" class="{{ $user->role->badgeColor() }} badge-sm" />
                @endscope

                @scope('cell_created_at', $user)
                {{ $user->created_at->format('d M Y') }}
                @endscope

                @scope('cell_actions', $user)
                <x-action-dropdown :model="$user">
                    <button wire:click="openEditDrawer('{{ $user->id }}')"
                        class="flex gap-2 items-center p-2 text-sm rounded">
                        <x-icon name="o-pencil" class="w-4 h-4" />
                        Edit
                    </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $user->id }}')"
                            wire:confirm="Are you sure you want to delete this user?"
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
        @if($users->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }}
                    pengguna
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>