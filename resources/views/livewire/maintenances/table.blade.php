<div class="shadow card bg-base-100">
    <div class="card-body">
        {{-- Header with Search and Action Buttons --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            {{-- Search Input --}}
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari perawatan aset..." icon="o-magnifying-glass"
                    class="input-sm" />
            </div>

            {{-- Filter Dropdowns --}}
            <div class="flex gap-2">
                {{-- Status Filter --}}
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm">
                            Status
                        </x-button>
                    </x-slot:trigger>
                    <x-menu-item title="Semua Status" wire:click="$set('statusFilter', '')" />
                    @foreach($statuses as $status)
                        <x-menu-item title="{{ $status->label() }}" wire:click="$set('statusFilter', '{{ $status->value }}')" />
                    @endforeach
                </x-dropdown>

                {{-- Type Filter --}}
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm">
                            Tipe
                        </x-button>
                    </x-slot:trigger>
                    <x-menu-item title="Semua Tipe" wire:click="$set('typeFilter', '')" />
                    @foreach($types as $type)
                        <x-menu-item title="{{ $type->label() }}" wire:click="$set('typeFilter', '{{ $type->value }}')" />
                    @endforeach
                </x-dropdown>

                {{-- Priority Filter --}}
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-funnel" class="btn-sm">
                            Prioritas
                        </x-button>
                    </x-slot:trigger>
                    <x-menu-item title="Semua Prioritas" wire:click="$set('priorityFilter', '')" />
                    @foreach($priorities as $priority)
                        <x-menu-item title="{{ $priority->label() }}" wire:click="$set('priorityFilter', '{{ $priority->value }}')" />
                    @endforeach
                </x-dropdown>
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'asset', 'label' => 'Aset'],
                    ['key' => 'title', 'label' => 'Judul'],
                    ['key' => 'type', 'label' => 'Tipe'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'priority', 'label' => 'Prioritas'],
                    ['key' => 'schedule', 'label' => 'Jadwal', 'class' => '!w-40'],
                    ['key' => 'assigned', 'label' => 'Teknisi'],
                    ['key' => 'actions', 'label' => 'Aksi'],
                ];
            @endphp
            <x-table :headers="$headers" :rows="$maintenances" striped show-empty-text selectable wire:model="selectedMaintenances">

                @scope('cell_asset', $maintenance)
                <div class="flex gap-2 items-center">
                    @if (!$maintenance->asset->image)
                        <div
                            class="flex justify-center items-center font-bold rounded-lg border-2 size-13 bg-base-300 border-base-100">
                            <x-icon name="o-photo" class="w-6 h-6 text-base-content/60" />
                        </div>
                    @else
                        <x-avatar :image="asset('storage/'.$maintenance->asset->image)"
                            class="!w-13 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                        </x-avatar>
                    @endif
                    <div>
                        <div class="font-mono text-xs truncate text-base-content/60">{{ $maintenance->asset->code }}</div>      
                        <div class="font-medium">{{ $maintenance->asset->name }}</div>
                        <div class="text-xs text-base-content/60">Tag: {{ $maintenance->asset->tag_code }}</div>
                    </div>
                </div>
                @endscope

                
                @scope('cell_title', $maintenance)
                <div class="flex flex-col">
                    <div class="font-medium">{{ $maintenance->title }}</div>
                    <div class="text-xs whitespace-nowrap text-base-content/60">{{ $maintenance->code ?? '-' }}</div>
                </div>
                @endscope
                
                @scope('cell_type', $maintenance)
                <x-badge value="{{ $maintenance->type->label() }}" class="badge-outline badge-{{ $maintenance->type->color() }} badge-sm" />
                @endscope

                @scope('cell_status', $maintenance)
                <x-badge value="{{ $maintenance->status->label() }}" class="badge-{{ $maintenance->status->color() }} badge-sm" />
                @endscope

                @scope('cell_priority', $maintenance)
                <x-badge value="{{ $maintenance->priority->label() }}" class="badge-outline badge-{{ $maintenance->priority->color() }} badge-sm" />
                @endscope

                @scope('cell_schedule', $maintenance)
                <div class="flex flex-col text-xs">
                    @if($maintenance->started_at)
                        <span>Mulai: {{ $maintenance->started_at->format('d M Y') }}</span>
                    @endif
                    @if($maintenance->estimated_completed_at)
                        <span>Estimasi: {{ $maintenance->estimated_completed_at->format('d M Y') }}</span>
                    @endif
                    @if($maintenance->completed_at)
                        <span>Selesai: {{ $maintenance->completed_at->format('d M Y') }}</span>
                    @endif
                </div>
                @endscope

                @scope('cell_assigned', $maintenance)
                <div class="tooltip">
                    <div class="text-xs tooltip-content">
                        <div class="font-medium">{{ $maintenance->employee?->full_name }}</div>
                    </div>
                    <x-avatar placeholder="{{ strtoupper(substr($maintenance->employee?->full_name, 0, 2)) }}"
                        class="!w-9 !bg-primary !font-bold border-2 border-base-100" />
                </div>
                @endscope

                @scope('cell_actions', $maintenance)
                <x-action-dropdown dropdown-id="maintenance-dropdown-{{ $maintenance->id }}" :model="$maintenance">
                    <li>
                        <a href="{{ route('maintenances.pdf', $maintenance) }}" target="_blank" onclick="document.getElementById('maintenance-dropdown-{{ $maintenance->id }}').hidePopover()">
                            <x-icon name="o-document" class="w-4 h-4" />
                            Unduh PDF
                        </a>
                    </li>
                    {{-- <li>
                        <button wire:click="$dispatch('open-edit-drawer', '{{ $maintenance->id }}')" onclick="document.getElementById('maintenance-dropdown-{{ $maintenance->id }}').hidePopover()" class="flex gap-2 items-center p-2 text-sm">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                    <li>
                        <button wire:click="$dispatch('open-complete-drawer', '{{ $maintenance->id }}')" onclick="document.getElementById('maintenance-dropdown-{{ $maintenance->id }}').hidePopover()" class="flex gap-2 items-center p-2 text-sm">
                            <x-icon name="o-check" class="w-4 h-4" />
                            Selesaikan
                        </button>
                    </li> --}}
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($maintenances->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $maintenances->firstItem() }}-{{ $maintenances->lastItem() }} dari {{ $maintenances->total() }}
                    perawatan
                </div>

                {{-- Livewire Pagination --}}
                <div class="mt-4">
                    {{ $maintenances->links() }}
                </div>
            </div>
        @endif
    </div>
</div>