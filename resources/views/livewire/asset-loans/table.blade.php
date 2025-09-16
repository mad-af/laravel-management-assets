<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari pinjaman (nama peminjam, asset)..."
                    icon="o-magnifying-glass" class="input-sm" />
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
                    <x-menu-item title="Dikembalikan" wire:click="$set('statusFilter', 'returned')" />
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-exclamation-triangle" class="btn-sm btn-outline">
                            Filter Kondisi
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Kondisi" wire:click="$set('conditionFilter', '')" />
                    @foreach($conditions as $condition)
                        <x-menu-item title="{{ $condition->label() }}"
                            wire:click="$set('conditionFilter', '{{ $condition->value }}')" />
                    @endforeach
                </x-dropdown>

                <x-checkbox wire:model.live="overdueFilter" label="Terlambat" class="checkbox-sm" />
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'asset', 'label' => 'Asset'],
                    ['key' => 'borrower_name', 'label' => 'Peminjam'],
                    ['key' => 'checkout_at', 'label' => 'Tgl Pinjam'],
                    ['key' => 'due_at', 'label' => 'Tgl Jatuh Tempo'],
                    ['key' => 'checkin_at', 'label' => 'Tgl Kembali'],
                    ['key' => 'condition', 'label' => 'Kondisi'],
                    ['key' => 'status', 'label' => 'Status', 'class' => 'w-32'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$assetLoans" striped show-empty-text>
                @scope('cell_asset', $assetLoan)
                <div class="flex flex-col">
                    <span class="font-medium">{{ $assetLoan->asset->name }}</span>
                    <span class="text-xs text-base-content/60">{{ $assetLoan->asset->code }}</span>
                </div>
                @endscope

                @scope('cell_borrower_name', $assetLoan)
                <span class="font-medium">{{ $assetLoan->borrower_name }}</span>
                @endscope

                @scope('cell_checkout_at', $assetLoan)
                {{ $assetLoan->checkout_at->format('d M Y') }}
                @endscope

                @scope('cell_due_at', $assetLoan)
                <div class="flex flex-col">
                    <span class="{{ $assetLoan->isOverdue() ? 'text-error font-medium' : '' }}">
                        {{ $assetLoan->due_at->format('d M Y') }}
                    </span>
                    @if($assetLoan->isOverdue())
                        <span class="text-xs text-error">Terlambat</span>
                    @endif
                </div>
                @endscope

                @scope('cell_checkin_at', $assetLoan)
                @if($assetLoan->checkin_at)
                    {{ $assetLoan->checkin_at->format('d M Y') }}
                @else
                    <span class="text-warning">Belum dikembalikan</span>
                @endif
                @endscope

                @scope('cell_condition', $assetLoan)
                <div class="flex flex-col gap-1">
                    @if($assetLoan->condition_out)
                        <div class="flex gap-1 items-center">
                            <span class="text-xs">Keluar:</span>
                            <x-badge value="{{ $assetLoan->condition_out->label() }}" 
                                class="{{ $assetLoan->condition_out->badgeColor() }} badge-xs" />
                        </div>
                    @endif
                    @if($assetLoan->condition_in)
                        <div class="flex gap-1 items-center">
                            <span class="text-xs">Masuk:</span>
                            <x-badge value="{{ $assetLoan->condition_in->label() }}" 
                                class="{{ $assetLoan->condition_in->badgeColor() }} badge-xs" />
                        </div>
                    @endif
                </div>
                @endscope

                @scope('cell_status', $assetLoan)
                @if($assetLoan->isActive())
                    @if($assetLoan->isOverdue())
                        <x-badge value="Terlambat" class="badge-error badge-sm" />
                    @else
                        <x-badge value="Aktif" class="badge-info badge-sm" />
                    @endif
                @else
                    <x-badge value="Dikembalikan" class="badge-success badge-sm" />
                @endif
                @endscope

                @scope('cell_actions', $assetLoan)
                <x-action-dropdown :model="$assetLoan">
                    <li>
                        <button wire:click="openEditDrawer('{{ $assetLoan->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded" onclick="document.getElementById('dropdown-menu-{{ $assetLoan->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    @if($assetLoan->isActive())
                        <li>
                            <button wire:click="returnAsset('{{ $assetLoan->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded text-success" onclick="document.getElementById('dropdown-menu-{{ $assetLoan->id }}').hidePopover()">
                                <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                                Kembalikan
                            </button>
                        </li>
                    @endif
                    <li>
                        <button wire:click="delete('{{ $assetLoan->id }}')"
                            wire:confirm="Are you sure you want to delete this loan record?"
                            class="flex gap-2 items-center p-2 text-sm rounded text-error" onclick="document.getElementById('dropdown-menu-{{ $assetLoan->id }}').hidePopover()">
                            <x-icon name="o-trash" class="w-4 h-4" />
                            Delete
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($assetLoans->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $assetLoans->firstItem() }}-{{ $assetLoans->lastItem() }} dari {{ $assetLoans->total() }}
                    pinjaman
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $assetLoans->links() }}
                </div>
            </div>
        @endif
    </div>
</div>