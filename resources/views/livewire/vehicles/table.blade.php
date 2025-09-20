<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Search vehicles (name, license plate, brand)..." icon="o-magnifying-glass"
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

                    <x-menu-item title="All Status" wire:click="$set('statusFilter', '')" />
                    @foreach(\App\Enums\AssetStatus::cases() as $status)
                        <x-menu-item title="{{ $status->label() }}" wire:click="$set('statusFilter', '{{ $status->value }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'name', 'label' => 'Vehicle Name'],
                    ['key' => 'code', 'label' => 'Asset Code'],
                    ['key' => 'license_plate', 'label' => 'License Plate'],
                    ['key' => 'brand_model', 'label' => 'Brand & Model'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'location', 'label' => 'Location'],
                    ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-20'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$vehicles" striped show-empty-text>
                @scope('cell_name', $vehicle)
                <span class="font-medium">{{ $vehicle->name }}</span>
                @endscope

                @scope('cell_code', $vehicle)
                <span class="font-mono text-sm">{{ $vehicle->code }}</span>
                @endscope

                @scope('cell_license_plate', $vehicle)
                <span class="font-mono font-medium">{{ $vehicle->vehicleProfile?->plate_no ?? '-' }}</span>
                @endscope

                @scope('cell_brand_model', $vehicle)
                <div class="text-sm">
                    <div class="font-medium">{{ $vehicle->vehicleProfile?->brand ?? '-' }}</div>
                    <div class="text-base-content/70">{{ $vehicle->vehicleProfile?->model ?? '-' }}</div>
                </div>
                @endscope

                @scope('cell_status', $vehicle)
                @php
                    $statusColors = [
                        'active' => 'badge-success',
                        'damaged' => 'badge-error',
                        'lost' => 'badge-error',
                        'maintenance' => 'badge-warning',
                        'checked_out' => 'badge-info',
                    ];
                    $statusColor = $statusColors[$vehicle->status->value] ?? 'badge-neutral';
                @endphp
                <x-badge value="{{ ucfirst($vehicle->status->value) }}" class="{{ $statusColor }} badge-sm" />
                @endscope

                @scope('cell_location', $vehicle)
                <span class="text-sm">{{ $vehicle->location?->name ?? '-' }}</span>
                @endscope

                @scope('cell_actions', $vehicle)
                <x-action-dropdown :model="$vehicle">
                    <li>
                        <button wire:click="openOdometerDrawer('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded text-primary" onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-calculator" class="w-4 h-4" />
                            Save Odometer
                        </button>
                    </li>
                    <li>
                        <button wire:click="viewDetail('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded" onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-eye" class="w-4 h-4" />
                            Detail
                        </button>
                    </li>
                    <li>
                        <button wire:click="openProfileDrawer('{{ $vehicle->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded" onclick="document.getElementById('dropdown-menu-{{ $vehicle->id }}').hidePopover()">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit Profile
                        </button>
                    </li>
                    <li>
                        <button wire:click="delete('{{ $vehicle->id }}')"
                            wire:confirm="Are you sure you want to delete this vehicle?"
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