<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari klaim..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            {{-- Filter Dropdown --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm ">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Draft" wire:click="$set('statusFilter', 'draft')" />
                    <x-menu-item title="Diajukan" wire:click="$set('statusFilter', 'submitted')" />
                    <x-menu-item title="Disetujui" wire:click="$set('statusFilter', 'approved')" />
                    <x-menu-item title="Ditolak" wire:click="$set('statusFilter', 'rejected')" />
                </x-dropdown>

                <x-button icon="o-plus" class="btn-sm" wire:click="openDrawer">Tambah</x-button>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'claim_no', 'label' => 'No. Klaim'],
                    ['key' => 'policy', 'label' => 'Polis / Asuransi'],
                    ['key' => 'asset', 'label' => 'Asset'],
                    ['key' => 'incident_date', 'label' => 'Tanggal Insiden'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$claims" striped show-empty-text>
                @scope('cell_claim_no', $claim)
                <span class="font-medium">{{ $claim->claim_no }}</span>
                @endscope

                @scope('cell_policy', $claim)
                <div class="text-sm">
                    <div class="font-medium">{{ $claim->policy?->policy_no ?? '-' }}</div>
                    <div class="text-base-content/70">{{ $claim->policy?->insurance?->name ?? '-' }}</div>
                </div>
                @endscope

                @scope('cell_asset', $claim)
                <span class="text-sm">{{ $claim->asset?->name ?? '-' }}</span>
                @endscope

                @scope('cell_incident_date', $claim)
                {{ optional($claim->incident_date)->format('d M Y') ?? '-' }}
                @endscope

                @scope('cell_status', $claim)
                @php $color = $claim->status?->color() ?? 'neutral'; $label = $claim->status?->label() ?? ucfirst($claim->status); @endphp
                <x-badge :value="$label" :class="'badge-' . $color . ' badge-sm'" />
                @endscope

                @scope('cell_actions', $claim)
                <x-action-dropdown :model="$claim">
                    <li>
                        <button wire:click="openEditDrawer('{{ $claim->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $claim->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $claim->id }}')"
                            wire:confirm="Anda yakin ingin menghapus klaim ini?"
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
        @if($claims->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $claims->firstItem() }}-{{ $claims->lastItem() }} dari {{ $claims->total() }} klaim
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $claims->links() }}
                </div>
            </div>
        @endif
    </div>
</div>