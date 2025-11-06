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
                        <x-button icon="o-funnel" class="btn-sm">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Draft" wire:click="$set('statusFilter', 'draft')" />
                    <x-menu-item title="Diajukan" wire:click="$set('statusFilter', 'submitted')" />
                    <x-menu-item title="Disetujui" wire:click="$set('statusFilter', 'approved')" />
                    <x-menu-item title="Ditolak" wire:click="$set('statusFilter', 'rejected')" />
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'claim_no', 'label' => 'No. Klaim'],
                    ['key' => 'policy', 'label' => 'Polis / Asuransi'],
                    ['key' => 'incident_date', 'label' => 'Tanggal Insiden'],
                    ['key' => 'incident_type', 'label' => 'Jenis Insiden'],
                    ['key' => 'amount_paid', 'label' => 'Jumlah Dibayar', 'class' => 'text-right'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$claims" striped show-empty-text>
                @scope('cell_claim_no', $claim)
                <span class="font-medium">{{ $claim->claim_no ?? '-' }}</span>
                @endscope

                @scope('cell_policy', $claim)
                <div class="text-sm">
                    <div class="text-xs truncate text-base-content/60">{{ $claim->policy?->policy_no ?? '-' }}</div>
                    <div class="font-medium truncate">{{ $claim->asset?->name ?? '-' }}</div>
                    <div class="text-xs truncate text-base-content/70">{{ $claim->policy?->insurance?->name ?? '-' }}
                    </div>
                </div>
                @endscope

                @scope('cell_incident_date', $claim)
                {{ optional($claim->incident_date)->format('d M Y') ?? '-' }}
                @endscope

                @scope('cell_incident_type', $claim)
                {{ $claim->incident_type?->label() ?? '-' }}
                @endscope

                @scope('cell_amount_paid', $claim)
                @if ($claim->amount_paid)
                    {{ 'Rp ' . number_format($claim->amount_paid, 0, ',', '.') }}
                @else
                    <span class="text-xs text-base-content/60">Belum ditentukan</span>
                @endif
                @endscope


                @scope('cell_status', $claim)
                <x-badge :value="$label = $claim->status?->label()" :class="'badge-' . $claim->status?->color() . ' badge-sm badge-outline badge-soft'" />
                @endscope

                @scope('cell_actions', $claim)
                <x-action-dropdown 
                    :model="$claim" 
                    :disabled="! in_array($claim->status, [
                        \App\Enums\InsuranceClaimStatus::DRAFT,
                        \App\Enums\InsuranceClaimStatus::SUBMITTED
                    ])"
                >
                    @if($claim->status == \App\Enums\InsuranceClaimStatus::DRAFT)
                        <li>
                            <button wire:click="openEditDrawer('{{ $claim->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $claim->id }}').hidePopover()">
                                <x-icon name="o-arrow-up-tray" class="w-4 h-4" />
                                Ajukan Klaim
                            </button>
                        </li>
                    @elseif($claim->status == \App\Enums\InsuranceClaimStatus::SUBMITTED)
                        <li>
                            <button wire:click="delete('{{ $claim->id }}')"
                                wire:confirm="Anda yakin ingin menghapus klaim ini?"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-check-circle" class="w-4 h-4" />
                                Verifikasi Klaim
                            </button>
                        </li>
                    @endif
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
                    {{ $claims->links(view: 'components.pagination.simple') }}
                </div>
            </div>
        @endif
    </div>
</div>