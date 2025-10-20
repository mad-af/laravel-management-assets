<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        
        {{-- Action Tabs (Delivery / Confirmation) --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                @foreach($transferActions ?? \App\Enums\AssetTransferAction::cases() as $action)
                    <label class="gap-2 tab">
                        <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                            wire:model.live="actionFilter" value="{{ $action->value }}" />
                        {{ $action->label() }}
                        <x-badge class="badge-{{ $action->color() }}" :value="$actionCounts[$action->value] ?? 0" />
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Header with Search and Action Tabs --}}
        <div class="flex flex-col gap-4 mb-4">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari transfer (nomor, alasan, cabang)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>

        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'transfer_no', 'label' => 'No. Transfer'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'items_count', 'label' => 'Jumlah Item'],
                    ['key' => 'branches_move', 'label' => 'Perpindahan Cabang'],
                    ['key' => 'reason', 'label' => 'Alasan'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$transfers" striped show-empty-text>
                @scope('cell_transfer_no', $transfer)
                <span class="font-medium">{{ $transfer->transfer_no }}</span>
                @endscope

                @scope('cell_status', $transfer)
                @php
                    $statusColors = [
                        'shipped' => 'badge-info',
                        'delivered' => 'badge-success',
                    ];
                    $statusColor = $statusColors[$transfer->status->value] ?? 'badge-neutral';
                @endphp
                <x-badge value="{{ $transfer->status->label() }}" class="{{ $statusColor }} badge-sm" />
                @endscope

                @scope('cell_items_count', $transfer)
                <x-badge value="{{ $transfer->items_count ?? $transfer->items->count() }} item"
                    class="badge-neutral badge-outline badge-sm" />
                @endscope

                @scope('cell_branches_move', $transfer)
                <div class="text-sm">
                    <span class="font-medium">{{ $transfer->fromBranch?->name ?? '-' }}</span>
                    <span class="mx-1 text-base-content/70">→</span>
                    <span class="font-medium">{{ $transfer->toBranch?->name ?? '-' }}</span>
                </div>
                @endscope

                @scope('cell_reason', $transfer)
                <div class="max-w-xs">
                    <p class="truncate" title="{{ $transfer->reason }}">{{ $transfer->reason }}</p>
                </div>
                @endscope

                @scope('cell_actions', $transfer)
                <x-action-dropdown :model="$transfer">
                    <li>
                        <button wire:click="viewDetail('{{ $transfer->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $transfer->id }}').hidePopover()">
                            <x-icon name="o-eye" class="w-4 h-4" />
                            Detail
                        </button>
                    </li>
                    <li>
                        <button wire:click="openEditDrawer('{{ $transfer->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $transfer->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $transfer->id }}')"
                            wire:confirm="Are you sure you want to delete this transfer?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error"
                            onclick="document.getElementById('dropdown-menu-{{ $transfer->id }}').hidePopover()">
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