<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari transfer (nomor, alasan, lokasi)..." icon="o-magnifying-glass"
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
                    <x-menu-item title="Draft" wire:click="$set('statusFilter', 'draft')" />
                    <x-menu-item title="Pending" wire:click="$set('statusFilter', 'pending')" />
                    <x-menu-item title="Approved" wire:click="$set('statusFilter', 'approved')" />
                    <x-menu-item title="Rejected" wire:click="$set('statusFilter', 'rejected')" />
                    <x-menu-item title="Executed" wire:click="$set('statusFilter', 'executed')" />
                    <x-menu-item title="Cancelled" wire:click="$set('statusFilter', 'cancelled')" />
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'transfer_no', 'label' => 'No. Transfer'],
                    ['key' => 'reason', 'label' => 'Alasan'],
                    ['key' => 'locations', 'label' => 'Lokasi'],
                    ['key' => 'items_count', 'label' => 'Jumlah Item'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'scheduled_at', 'label' => 'Dijadwalkan'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$transfers" striped show-empty-text>
                @scope('cell_transfer_no', $transfer)
                <span class="font-medium">{{ $transfer->transfer_no }}</span>
                @endscope

                @scope('cell_reason', $transfer)
                <div class="max-w-xs">
                    <p class="truncate" title="{{ $transfer->reason }}">{{ $transfer->reason }}</p>
                </div>
                @endscope

                @scope('cell_locations', $transfer)
                <div class="text-sm">
                    <div class="flex gap-1 items-center">
                        <span class="text-base-content/70">Dari:</span>
                        <span class="font-medium">{{ $transfer->fromLocation?->name ?? '-' }}</span>
                    </div>
                    <div class="flex gap-1 items-center">
                        <span class="text-base-content/70">Ke:</span>
                        <span class="font-medium">{{ $transfer->toLocation?->name ?? '-' }}</span>
                    </div>
                </div>
                @endscope

                @scope('cell_items_count', $transfer)
                <x-badge value="{{ $transfer->items_count ?? $transfer->items->count() }} item" class="badge-neutral badge-outline badge-sm" />
                @endscope

                @scope('cell_status', $transfer)
                @php
                    $statusColors = [
                        'draft' => 'badge-ghost',
                        'pending' => 'badge-warning',
                        'approved' => 'badge-info',
                        'rejected' => 'badge-error',
                        'executed' => 'badge-success',
                        'cancelled' => 'badge-neutral',
                    ];
                    $statusColor = $statusColors[$transfer->status->value] ?? 'badge-neutral';
                @endphp
                <x-badge value="{{ $transfer->status->label() }}" class="{{ $statusColor }} badge-sm" />
                @endscope

                @scope('cell_scheduled_at', $transfer)
                @if($transfer->scheduled_at)
                    <div class="text-sm">
                        <div>{{ $transfer->scheduled_at->format('d M Y') }}</div>
                        <div class="text-base-content/70">{{ $transfer->scheduled_at->format('H:i') }}</div>
                    </div>
                @else
                    <span class="text-base-content/50">-</span>
                @endif
                @endscope

                @scope('cell_actions', $transfer)
                <x-action-dropdown :model="$transfer">
                    <li>
                        <button wire:click="viewDetail('{{ $transfer->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded" onclick="document.activeElement.blur()">
                            <x-icon name="o-eye" class="w-4 h-4" />
                            Detail
                        </button>
                    </li>
                    <li>
                        <button wire:click="openEditDrawer('{{ $transfer->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded" onclick="document.activeElement.blur()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $transfer->id }}')"
                            wire:confirm="Are you sure you want to delete this transfer?"
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
        @if($transfers->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $transfers->firstItem() }}-{{ $transfers->lastItem() }} dari {{ $transfers->total() }}
                    transfer
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $transfers->links() }}
                </div>
            </div>
        @endif
    </div>
</div>