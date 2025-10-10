<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Search vehicles (name, license plate, brand)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>

            {{-- Filter Dropdown --}}
            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm">
                            Filter Status
                        </x-button>
                    </x-slot:trigger>

                    <x-menu-item title="All Status" wire:click="$set('statusFilter', '')" />
                    @foreach(\App\Enums\AssetStatus::cases() as $status)
                        <x-menu-item title="{{ $status->label() }}"
                            wire:click="$set('statusFilter', '{{ $status->value }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Nama Kendaraan'],
                    ['key' => 'current_odometer_km', 'label' => 'Odometer (km)', 'class' => 'text-right'],
                    ['key' => 'odometer_target', 'label' => 'Target Odometer', 'class' => 'text-right'],
                    ['key' => 'next_service', 'label' => 'Perawatan Selanjutnya'],
                    ['key' => 'license_plate', 'label' => 'Plat Nomor'],
                    ['key' => 'brand_model', 'label' => 'Brand & Model'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'condition', 'label' => 'Kondisi'],
                    ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$vehicles" striped show-empty-text>
                @scope('cell_name', $asset)
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
                        <div class="text-xs text-base-content/60">Tag: {{ $asset->tag_code }}</div>
                    </div>
                </div>
                @endscope

                @scope('cell_code', $vehicle)
                <span class="font-mono text-sm">{{ $vehicle->code }}</span>
                @endscope

                @scope('cell_current_odometer_km', $vehicle)
                <span class="text-sm">{{ 
                    $vehicle->vehicleProfile?->current_odometer_km ?
    number_format($vehicle->vehicleProfile?->current_odometer_km, 0, ',', '.')
    : '-' 
                }}</span>
                @endscope

                @scope('cell_odometer_target', $vehicle)
                <span class="text-sm">{{ 
                    $vehicle->vehicleProfile?->service_target_odometer_km ?
    number_format($vehicle->vehicleProfile?->service_target_odometer_km, 0, ',', '.')
    : '-' 
                }}</span>
                @endscope

                @scope('cell_next_service', $vehicle)
                @if($vehicle->vehicleProfile?->next_service_date)
                    @php
                        $serviceInfo = $this->formatNextServiceDate($vehicle->vehicleProfile->next_service_date);
                    @endphp
                    <div class="text-sm">
                        <div class="font-medium">{{ $serviceInfo['formatted_date'] }}</div>
                        <div class="text-xs {{ $serviceInfo['is_overdue'] ? 'text-error' : 'text-base-content/60' }}">
                            {{ $serviceInfo['time_info'] }}
                        </div>
                    </div>
                @else
                    <span class="text-sm">-</span>
                @endif
                @endscope

                @scope('cell_license_plate', $vehicle)
                <span
                    class="font-mono font-medium whitespace-nowrap">{{ $vehicle->vehicleProfile?->plate_no ?? '-' }}</span>
                @endscope

                @scope('cell_brand_model', $vehicle)
                <div class="text-sm">
                    <div class="font-medium">{{ $vehicle->vehicleProfile?->brand ?? '-' }}</div>
                    <div class="text-base-content/70">{{ $vehicle->vehicleProfile?->model ?? '-' }}</div>
                </div>
                @endscope

                @scope('cell_status', $vehicle)
                <x-badge value="{{ $vehicle->status->label() }}"
                    class="badge-{{ $vehicle->status->color() }} badge-sm" />
                @endscope

                @scope('cell_condition', $vehicle)
                <x-badge value="{{ $vehicle->condition->label() }}"
                    class="badge-{{ $vehicle->condition->color() }} badge-outline badge-sm" />
                @endscope

                @scope('cell_actions', $vehicle)
                <x-action-dropdown :model="$vehicle">
                    <li>
                        <button wire:click="viewDetail('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-eye" class="w-4 h-4" />
                            Detail
                        </button>
                    </li>
                    <li>
                        <button wire:click="openOdometerDrawer('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded text-primary"
                            onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-calculator" class="w-4 h-4" />
                            Tambah Odometer
                        </button>
                    </li>
                    <li>
                        <button wire:click="openProfileDrawer('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded"
                            onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-truck" class="w-4 h-4" />
                            Update Profil Kendaraan
                        </button>
                    </li>
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($vehicles->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Showing {{ $vehicles->firstItem() }}-{{ $vehicles->lastItem() }} of {{ $vehicles->total() }}
                    vehicles
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $vehicles->links() }}
                </div>
            </div>
        @endif
    </div>
</div>