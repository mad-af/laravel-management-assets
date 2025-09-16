<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari kategori..." icon="o-magnifying-glass"
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
                    ['key' => 'name', 'label' => 'Kategori'],
                    ['key' => 'is_active', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$categories" striped show-empty-text>
                @scope('cell_name', $category)
                <span class="font-medium">{{ $category->name }}</span>
                @endscope

                @scope('cell_company', $category)
                @if($category->company)
                    <div class="text-sm">{{ $category->company->name }}</div>
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                @scope('cell_is_active', $category)
                @if($category->is_active)
                    <x-badge value="Aktif" class="badge-success badge-sm" />
                @else
                    <x-badge value="Tidak Aktif" class="badge-error badge-sm" />
                @endif
                @endscope

                @scope('cell_created_at', $category)
                {{ $category->created_at->format('d M Y') }}
                @endscope

                @scope('cell_actions', $category)
                <x-action-dropdown :model="$category">
                    <button wire:click="openEditDrawer('{{ $category->id }}')"
                        class="flex gap-2 items-center p-2 text-sm rounded">
                        <x-icon name="o-pencil" class="w-4 h-4" />
                        Edit
                    </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $category->id }}')"
                            wire:confirm="Are you sure you want to delete this category?"
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
        @if($categories->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $categories->firstItem() }}-{{ $categories->lastItem() }} dari {{ $categories->total() }}
                    kategori
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        @endif
    </div>
</div>