<div>
    <div class="shadow card bg-base-100">
        <div class="card-body">
            {{-- Header with Search and Action Buttons --}}
            <div class="flex flex-col gap-4 mb-4 sm:flex-row">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-input wire:model.live="search" placeholder="Cari transfer no, reason..." icon="o-magnifying-glass"
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
                        @foreach(\App\Enums\AssetTransferStatus::cases() as $status)
                            <x-menu-item title="{{ $status->label() }}" wire:click="$set('statusFilter', '{{ $status->value }}')" />
                        @endforeach
                    </x-dropdown>
                </div>
            </div>

            {{-- Table --}}
            <div>
                @php
                    $headers = [
                        ['key' => 'transfer_no', 'label' => 'Transfer No'],
                        ['key' => 'status', 'label' => 'Status'],
                        ['key' => 'requested_by', 'label' => 'Requested By'],
                        ['key' => 'items_count', 'label' => 'Items'],
                        ['key' => 'scheduled_at', 'label' => 'Scheduled At'],
                        ['key' => 'created_at', 'label' => 'Created At'],
                        ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-32'],
                    ];
                @endphp
                <x-table :headers="$headers" :rows="$transfers" striped show-empty-text>
                    @scope('cell_transfer_no', $transfer)
                    <div>
                        <div class="font-medium">{{ $transfer->transfer_no }}</div>
                        @if($transfer->reason)
                            <div class="text-sm text-gray-500">{{ Str::limit($transfer->reason, 50) }}</div>
                        @endif
                    </div>
                    @endscope

                    @scope('cell_status', $transfer)
                    @php
                        $statusEnum = \App\Enums\AssetTransferStatus::tryFrom($transfer->status);
                        $statusColor = $statusEnum ? 'badge-' . $statusEnum->color() : 'badge-ghost';
                        $statusLabel = $statusEnum ? $statusEnum->label() : ucfirst($transfer->status);
                    @endphp
                    <x-badge value="{{ $statusLabel }}" class="{{ $statusColor }} badge-sm" />
                    @endscope

                    @scope('cell_requested_by', $transfer)
                    <div>
                        <div class="font-medium">{{ $transfer->requestedBy->name }}</div>
                        <div class="text-sm text-gray-500">{{ $transfer->requestedBy->email }}</div>
                    </div>
                    @endscope

                    @scope('cell_items_count', $transfer)
                    <x-badge value="{{ $transfer->items_count ?? $transfer->items->count() }} items" class="badge-outline badge-sm" />
                    @endscope

                    @scope('cell_scheduled_at', $transfer)
                    @if($transfer->scheduled_at)
                        {{ $transfer->scheduled_at->format('d M Y H:i') }}
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                    @endscope

                    @scope('cell_created_at', $transfer)
                    {{ $transfer->created_at->format('d M Y H:i') }}
                    @endscope

                    @scope('cell_actions', $transfer)
                    <div class="flex gap-1">
                        <a href="{{ route('asset-transfers.show', $transfer) }}" class="btn btn-ghost btn-xs">
                            <x-icon name="o-eye" class="w-4 h-4" />
                        </a>
                        @if($transfer->status === 'draft')
                            <a href="{{ route('asset-transfers.edit', $transfer) }}" class="btn btn-ghost btn-xs">
                                <x-icon name="o-pencil" class="w-4 h-4" />
                            </a>
                            <button wire:click="delete('{{ $transfer->id }}')"
                                wire:confirm="Are you sure you want to delete this transfer?"
                                class="btn btn-ghost btn-xs text-error">
                                <x-icon name="o-trash" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
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
</div>