<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        
        {{-- Status Taxes Tabs --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                @foreach($loanStatuses as $status)
                    <label class="gap-2 tab">
                        <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                            wire:model.live="statusFilter" value="{{ $status->value }}" />
                        {{ $status->label() }}
                        <x-badge class="badge-{{ $status->color() }}" :value="$statusCounts[$status->value] ?? 0" />
                    </label>
                @endforeach
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
                            Filter Kategori
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Kategori" wire:click="$set('categoryFilter', '')" />
                    @foreach($categories as $category)
                        <x-menu-item title="{!! $category->name !!}" wire:click="$set('categoryFilter', '{{ $category->id }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'asset', 'label' => 'Asset'],
                    ['key' => 'category_name', 'label' => 'Kategori'],
                    // ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'condition', 'label' => 'Kondisi'],
                    ['key' => 'checkout_at', 'label' => 'Tgl Pinjam'],
                    ['key' => 'due_at', 'label' => 'Tgl Jatuh Tempo'],
                    ['key' => 'borrower_name', 'label' => 'Peminjam'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$assets" striped show-empty-text>
                @scope('cell_asset', $asset)
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

                @scope('cell_category_name', $asset)
                <span>{{ optional($asset->category)->name ?? '-' }}</span>
                @endscope

                {{-- @scope('cell_status', $asset)
                <x-badge value="{{ $asset->asset_loan_status->label() }}" class="whitespace-nowrap badge-{{ $asset->asset_loan_status->color() }} badge-sm" />
                @endscope --}}

                @scope('cell_condition', $asset)
                <x-badge value="{{ $asset->condition->label() }}" class="whitespace-nowrap badge-outline badge-{{ $asset->condition->color() }} badge-sm" />
                @endscope

                @scope('cell_checkout_at', $asset)
                {{ optional($asset->currentLoan)->checkout_at?->format('d M Y') ?? '-' }}
                @endscope

                @scope('cell_due_at', $asset)
                @php $due = optional($asset->currentLoan)->due_at; @endphp
                <div class="flex flex-col">
                    <span class="{{ ($asset->asset_loan_status === \App\Enums\AssetLoanStatus::OVERTIME) ? 'text-error font-medium' : '' }}">
                        {{ $due ? $due->format('d M Y') : '-' }}
                    </span>
                    @if($asset->asset_loan_status === \App\Enums\AssetLoanStatus::OVERTIME)
                        <span class="text-xs text-error">Terlambat</span>
                    @endif
                </div>
                @endscope

                @scope('cell_borrower_name', $asset)
                @if (optional($asset->currentLoan)->employee)
                @php $name = optional(optional($asset->currentLoan)->employee)->full_name ?? '-' @endphp
                <div class="tooltip">
                    <div class="text-xs tooltip-content">
                        <div class="font-medium">{{ $name }}</div>
                    </div>
                    <x-avatar placeholder="{{ strtoupper(substr($name, 0, 2)) }}"
                        class="!w-9 !bg-primary !font-bold border-2 border-base-100" />
                </div>
                @else
                -
                @endif
                @endscope

                @scope('cell_actions', $asset)
                @php $loan = $asset->currentLoan; @endphp
                <x-action-dropdown :model="$asset">
                    @if($loan)
                        <li class="hidden">
                            <button wire:click="openEditDrawer('{{ $loan->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                                <x-icon name="o-pencil" class="w-4 h-4" />
                                Edit Pinjaman
                            </button>
                        </li>
                        <li>
                            <button wire:click="returnAsset('{{ $asset->id }}', '{{ $loan->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $asset->id }}').hidePopover()">
                                <x-icon name="o-archive-box-arrow-down" class="w-4 h-4" />
                                Pengembalian Asset
                            </button>
                        </li>
                        <li class="hidden">
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
                            <button wire:click="openDrawer('{{ $asset->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-tag" class="w-4 h-4" />
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