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
                        <x-button icon="o-funnel" class="btn-sm">
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
                        <x-button icon="o-tag" class="btn-sm">
                            Filter Kategori
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Kategori" wire:click="$set('categoryFilter', '')" />
                    @foreach($categories as $category)
                        <x-menu-item title="{{ $category->name }}"
                            wire:click="$set('categoryFilter', '{{ $category->id }}')" />
                    @endforeach
                </x-dropdown>

            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Nama Asset'],
                    ['key' => 'category', 'label' => 'Kategori'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'condition', 'label' => 'Kondisi'],
                    ['key' => 'last_seen_at', 'label' => 'Terakhir Dilihat'],
                    ['key' => 'actions', 'label' => 'Aksi'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$assets" striped show-empty-text selectable wire:model="selectedAssets">

                @scope('cell_name', $asset)
                <div class="flex gap-2 items-center">
                    @if (!$asset->image)
                        <div
                            class="flex justify-center items-center font-bold rounded-lg border-2 size-13 bg-base-300 border-base-100">
                            <x-icon name="o-photo" class="w-6 h-6 text-base-content/60" />
                        </div>
                    @else
                        <x-avatar :image="asset('storage/'.$asset->image)"
                            class="!w-13 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                        </x-avatar>
                    @endif
                    <div>
                        <div class="font-mono text-xs truncate text-base-content/60">{{ $asset->code }}</div>
                        <div class="font-medium">{{ $asset->name }}</div>
                        <div class="text-xs text-base-content/60">Tag: {{ $asset->tag_code }}</div>
                    </div>
                </div>
                @endscope

                @scope('cell_category', $asset)
                {{ $asset->category?->name ?? '-' }}
                @endscope

                @scope('cell_status', $asset)
                <x-badge value="{{ $asset->status->label() }}" class="badge-{{ $asset->status->color() }} badge-sm whitespace-nowrap" />
                @endscope

                @scope('cell_condition', $asset)
                <x-badge value="{{ $asset->condition->label() }}"
                    class="badge-outline badge-{{ $asset->condition->color() }} badge-sm whitespace-nowrap" />
                @endscope

                @scope('cell_last_seen_at', $asset)
                @if($asset->last_seen_at)
                    <span title="{{ $asset->last_seen_at->format('d M Y H:i:s') }}">
                        {{ $asset->last_seen_at->locale('id')->diffForHumans() }}
                    </span>
                @else
                    -
                @endif
                @endscope

                @scope('cell_actions', $asset)
                <x-action-dropdown :model="$asset">
                    {{-- Disabled --}}
                    <li>
                        <a href="{{ route('assets.show', $asset) }}"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()" >
                            <x-icon name="o-eye" class="w-4 h-4" />
                            View
                        </a>
                    </li>
                    <li>
                        <button wire:click="printQRBarcode('{{ $asset->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-qr-code" class="w-4 h-4" />
                            Print QR/Barcode
                        </button>
                    </li>
                    <li>
                        <button wire:click="openEditDrawer('{{ $asset->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    {{-- <li>
                        <button wire:click="delete('{{ $asset->id }}')"
                            wire:confirm="Are you sure you want to delete this asset?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error"
                            onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li> --}}
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