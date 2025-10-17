<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        {{-- Status Taxes Tabs --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                <label class="gap-2 tab">
                    <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                        wire:model.live="statusFilter" value="available" />
                    Tersedia
                    <x-badge class="badge-success" :value="$availableCount" />
                </label>
                <label class="gap-2 tab">
                    <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                        wire:model.live="statusFilter" value="on_loan" />
                    Dalam Peminjaman
                    <x-badge class="badge-warning" :value="$onLoanCount" />
                </label>
                <label class="gap-2 tab">
                    <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                        wire:model.live="statusFilter" value="overdue" />
                    Terlambat
                    <x-badge class="badge-error" :value="$overdueCount" />
                </label>
            </div>
        </div>

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
                        <x-button icon="o-funnel" class="btn-sm">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Aktif" wire:click="$set('statusFilter', 'active')" />
                    <x-menu-item title="Dikembalikan" wire:click="$set('statusFilter', 'returned')" />
                </x-dropdown>

                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-exclamation-triangle" class="btn-sm">
                            Filter Kondisi
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Kondisi" wire:click="$set('conditionFilter', '')" />
                    {{-- @foreach($conditions as $condition)
                        <x-menu-item title="{{ $condition->label() }}"
                            wire:click="$set('conditionFilter', '{{ $condition->value }}')" />
                    @endforeach --}}
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
            <x-table :headers="$headers" :rows="$assets" striped show-empty-text>
                @scope('cell_asset', $asset)
                <div class="flex flex-col">
                    <span class="font-medium">{{ $asset->name }}</span>
                    <span class="text-xs text-base-content/60">{{ $asset->code }}</span>
                </div>
                @endscope

                @scope('cell_borrower_name', $asset)
                <span class="font-medium">{{ optional(optional($asset->loans->first())->employee)->full_name ?? '-' }}</span>
                @endscope

                @scope('cell_checkout_at', $asset)
                {{ optional($asset->loans->first())->checkout_at?->format('d M Y') ?? '-' }}
                @endscope

                @scope('cell_due_at', $asset)
                @php $due = optional($asset->loans->first())->due_at; @endphp
                <div class="flex flex-col">
                    <span class="{{ ($asset->status === \App\Enums\AssetStatus::ON_LOAN && $due && $due->isPast()) ? 'text-error font-medium' : '' }}">
                        {{ $due ? $due->format('d M Y') : '-' }}
                    </span>
                    @if($asset->status === \App\Enums\AssetStatus::ON_LOAN && $due && $due->isPast())
                        <span class="text-xs text-error">Terlambat</span>
                    @endif
                </div>
                @endscope

                @scope('cell_checkin_at', $asset)
                @if($asset->status === \App\Enums\AssetStatus::ON_LOAN)
                    <span class="text-warning">Belum dikembalikan</span>
                @else
                    -
                @endif
                @endscope

                @scope('cell_condition', $asset)
                @php $loan = $asset->loans->first(); @endphp
                <div class="flex flex-col gap-1">
                    @if(optional($loan)->condition_out)
                        <div class="flex gap-1 items-center">
                            <span class="text-xs">Keluar:</span>
                            <x-badge value="{{ $loan->condition_out->label() }}"
                                class="{{ $loan->condition_out->badgeColor() }} badge-xs" />
                        </div>
                    @endif
                    @if(optional($loan)->condition_in)
                        <div class="flex gap-1 items-center">
                            <span class="text-xs">Masuk:</span>
                            <x-badge value="{{ $loan->condition_in->label() }}"
                                class="{{ $loan->condition_in->badgeColor() }} badge-xs" />
                        </div>
                    @endif
                    @if(!optional($loan)->condition_out && !optional($loan)->condition_in)
                        -
                    @endif
                </div>
                @endscope

                @scope('cell_status', $asset)
                @php $due = optional($asset->loans->first())->due_at; @endphp
                @if($asset->status === \App\Enums\AssetStatus::ON_LOAN && $due && $due->isPast())
                    <x-badge value="Terlambat" class="whitespace-nowrap badge-error badge-sm" />
                @elseif($asset->status === \App\Enums\AssetStatus::ON_LOAN)
                    <x-badge value="Dalam Peminjaman" class="whitespace-nowrap badge-warning badge-sm" />
                @else
                    <x-badge value="Tersedia" class="whitespace-nowrap badge-success badge-sm" />
                @endif
                @endscope

                @scope('cell_actions', $asset)
                @php $loan = $asset->loans->first(); @endphp
                <x-action-dropdown :model="$asset">
                    @if($loan)
                        <li>
                            <button wire:click="openEditDrawer('{{ $loan->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                                <x-icon name="o-pencil" class="w-4 h-4" />
                                Edit Pinjaman
                            </button>
                        </li>
                        <li>
                            <button wire:click="returnAsset('{{ $loan->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded text-success"
                                onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                                <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                                Kembalikan
                            </button>
                        </li>
                        <li>
                            <button wire:click="delete('{{ $loan->id }}')"
                                wire:confirm="Are you sure you want to delete this loan record?"
                                class="flex gap-2 items-center p-2 text-sm rounded text-error"
                                onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                                <x-icon name="o-trash" class="w-4 h-4" />
                                Hapus
                            </button>
                        </li>
                    @else
                        <li>
                            <button wire:click="openDrawer"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-plus" class="w-4 h-4" />
                                Pinjamkan Asset
                            </button>
                        </li>
                    @endif
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($assets->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }} dari {{ $assets->total() }} aset
                </div>
                <div class="mt-4">
                    {{ $assets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>