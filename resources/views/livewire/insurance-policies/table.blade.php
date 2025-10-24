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
                    ['key' => 'end_date', 'label' => 'Selesai'],
                    ['key' => 'status', 'label' => 'Status'],
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
                        <x-avatar :image="asset('storage/' . $asset->image)"
                            class="!w-13 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                        </x-avatar>
                    @endif
                    <div>
                        <div class="font-mono text-xs truncate text-base-content/60">{{ $asset->code }}</div>
                        <div class="font-medium">{{ $asset->name }}</div>
                        <div class="text-xs whitespace-nowrap text-base-content/60">Tag: {{ $asset->tag_code }}</div>
                    </div>
                </div>
                @endscope

                @scope('cell_insurance', $asset)
                <div class="tooltip" data-tip="{{ optional($asset->latestActiveInsurancePolicy)->insurance->name }}">
                    <x-avatar placeholder="{{ strtoupper(substr(optional($asset->latestActiveInsurancePolicy)->insurance->name, 0, 2)) }}"
                            class="!w-9 !rounded-lg !bg-primary !font-bold border-2 border-base-100" />
                </div>
                @endscope

                @scope('cell_policy_no', $asset)
                {{ optional($asset->latestActiveInsurancePolicy)->policy_no ?? '-' }}
                @endscope

                @scope('cell_policy_type', $asset)
                @php $policy = $asset->latestActiveInsurancePolicy; @endphp
                @if($policy && $policy->policy_type)
                    <x-badge :value="$policy->policy_type->label()" :class="'badge-' . $policy->policy_type->color() . ' badge-sm whitespace-nowrap badge-soft badge-outline'" />
                @else
                    -
                @endif
                @endscope


                @scope('cell_end_date', $asset)
                @if ($asset->latestActiveInsurancePolicy)
                    <span class="">
                        {{ optional(value: optional($asset->latestActiveInsurancePolicy)->end_date)->format('d M Y') }}
                    </span>
                @else
                    -
                @endif
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
                        @if ($asset->latestActiveInsurancePolicy->status->value == 'inactive')
                        <li>
                            <button wire:click="openDrawer('{{ $policy->asset_id }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded"
                                onclick="document.getElementById('dropdown-menu-{{ $policy->id }}').hidePopover()">
                                <x-icon name="o-plus" class="w-4 h-4" />
                                Perbarui Polis
                            </button>
                        </li>
                        @endif
                        
                        @if ($asset->latestActiveInsurancePolicy->status->value == 'active')
                        <li>
                            <button wire:click="delete('{{ $policy->id }}')"
                                wire:confirm="Apakah Anda yakin ingin menghapus polis ini?"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-wallet" class="w-4 h-4" />
                                Klaim Asuransi
                            </button>
                        </li>
                        @endif
                        
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