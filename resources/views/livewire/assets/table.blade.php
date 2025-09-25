<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari asset (nama, kode, tag)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>

            {{-- Filter Dropdowns --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm ">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    @foreach($statuses as $status)
                        <x-menu-item title="{{ $status->label() }}"
                            wire:click="$set('statusFilter', '{{ $status->value }}')" />
                    @endforeach
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-tag" class="btn-sm ">
                            Filter Kategori
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Kategori" wire:click="$set('categoryFilter', '')" />
                    @foreach($categories as $category)
                        <x-menu-item title="{{ $category->name }}"
                            wire:click="$set('categoryFilter', '{{ $category->id }}')" />
                    @endforeach
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-map-pin" class="btn-sm ">
                            Filter Lokasi
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Lokasi" wire:click="$set('locationFilter', '')" />
                    @foreach($locations as $location)
                        <x-menu-item title="{{ $location->name }}"
                            wire:click="$set('locationFilter', '{{ $location->id }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'code', 'label' => 'Kode Asset', 'class' => 'w-40'],
                    ['key' => 'name', 'label' => 'Nama Asset'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'location', 'label' => 'Lokasi'],
                    ['key' => 'status', 'label' => 'Status', 'class' => 'w-44'],
                    ['key' => 'value', 'label' => 'Nilai', 'class' => 'w-36'],
                    ['key' => 'created_at', 'label' => 'Dibuat'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$assets" striped show-empty-text>
                @scope('cell_code', $asset)
                <div class="flex flex-col">
                    <span class="font-medium">{{ $asset->code }}</span>
                    @if($asset->tag_code)
                        <span class="text-xs text-base-content/60">Tag: {{ $asset->tag_code }}</span>
                    @endif
                </div>
                @endscope

                @scope('cell_name', $asset)
                <span class="font-medium">{{ $asset->name }}</span>
                @endscope

                @scope('cell_category', $asset)
                {{ $asset->category?->name ?? '-' }}
                @endscope

                @scope('cell_location', $asset)
                {{ $asset->location?->name ?? '-' }}
                @endscope

                @scope('cell_status', $asset)
                <x-badge value="{{ $asset->status->label() }}" class="{{ $asset->status->badgeColor() }} badge-sm" />
                @endscope

                @scope('cell_value', $asset)
                @if($asset->value)
                    Rp {{ number_format($asset->value, 0, ',', '.') }}
                @else
                    -
                @endif
                @endscope

                @scope('cell_created_at', $asset)
                {{ $asset->created_at->format('d M Y') }}
                @endscope

                @scope('cell_actions', $asset)
                <x-action-dropdown :model="$asset">
                    <li>
                        <a href="{{ route('assets.show', $asset) }}"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-eye" class="w-4 h-4" />
                            View
                        </a>
                    </li>
                    <li>
                        <a
                            onclick="printQRBarcode('{{ $asset->tag_code }}', '{{ $asset->name }}', '{{ $asset->code }}', '{{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '' }}'); document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-qr-code" class="w-4 h-4" />
                            Print QR/Barcode
                        </a>
                    </li>
                    <li>
                        <button wire:click="openEditDrawer('{{ $asset->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $asset->id }}')"
                            wire:confirm="Are you sure you want to delete this asset?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($assets->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }} dari {{ $assets->total() }}
                    asset
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $assets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>