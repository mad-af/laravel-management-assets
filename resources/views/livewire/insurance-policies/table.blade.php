<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari polis..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            {{-- Filter Dropdown & Add Button --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm ">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    <x-menu-item title="Aktif" wire:click="$set('statusFilter', 'active')" />
                    <x-menu-item title="Tidak Aktif" wire:click="$set('statusFilter', 'inactive')" />
                </x-dropdown>

                <x-button icon="o-plus" class="btn-sm" wire:click="openDrawer">Tambah</x-button>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'asset', 'label' => 'Aset'],
                    ['key' => 'insurance', 'label' => 'Provider'],
                    ['key' => 'policy_no', 'label' => 'No Polis'],
                    ['key' => 'policy_type', 'label' => 'Tipe Polis'],
                    ['key' => 'start_date', 'label' => 'Mulai'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$assets" striped show-empty-text>
                @scope('cell_asset', $asset)
                <span class="font-medium">{{ $asset->name }}</span>
                @endscope

                @scope('cell_insurance', $asset)
                {{ optional($asset->latestActiveInsurancePolicy)->insurance->name ?? '-' }}
                @endscope

                @scope('cell_policy_no', $asset)
                {{ optional($asset->latestActiveInsurancePolicy)->policy_no ?? '-' }}
                @endscope

                @scope('cell_policy_type', $asset)
                @php $policy = $asset->latestActiveInsurancePolicy; @endphp
                @if($policy && $policy->policy_type)
                    <x-badge :value="$policy->policy_type->label()" :class="'badge-' . $policy->policy_type->color() . ' badge-sm'" />
                @else
                    -
                @endif
                @endscope

                @scope('cell_start_date', $asset)
                {{ optional(optional($asset->latestActiveInsurancePolicy)->start_date)->format('d M Y') }}
                @endscope

                @scope('cell_status', $asset)
                @php $policy = $asset->latestActiveInsurancePolicy; @endphp
                @if($policy && $policy->status)
                    <x-badge :value="$policy->status->label()" :class="'badge-' . $policy->status->color() . ' badge-sm'" />
                @else
                    -
                @endif
                @endscope

                @scope('cell_actions', $asset)
                @php $policy = $asset->latestActiveInsurancePolicy; @endphp
                @if($policy)
                    <x-action-dropdown :model="$policy">
                        <li>
                            <button wire:click="openEditDrawer('{{ $policy->id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $policy->id }}').hidePopover()">
                                <x-icon name="o-pencil" class="w-4 h-4" />
                                Edit
                            </button>
                        </li>
                        <li>
                            <button wire:click="delete('{{ $policy->id }}')"
                                wire:confirm="Apakah Anda yakin ingin menghapus polis ini?"
                                class="flex gap-2 items-center p-2 text-sm rounded text-error">
                                <x-icon name="o-trash" class="w-4 h-4" />
                                Delete
                            </button>
                        </li>
                    </x-action-dropdown>
                @else
                    <div class="text-sm text-base-content/70">Tidak ada polis aktif</div>
                @endif
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($assets->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $assets->firstItem() }}-{{ $assets->lastItem() }} dari {{ $assets->total() }} aset
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $assets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>