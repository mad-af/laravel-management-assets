<div class="flex flex-col h-full">
    <!-- Column Header -->
    <div class="flex justify-between items-center p-4 py-2 border-b border-base-300 bg-base-100">
        <h3 class="text-sm font-semibold text-base-content">{{ $title }}</h3>
        <span class="w-6 text-center text-xs badge-xs badge {{ $badgeColorClass }}">
            {{ $maintenances->count() }}
        </span>
    </div>

    <!-- Column Content -->
    <div class="overflow-y-auto flex-1 px-2 pt-2 space-y-2 rounded-lg">
        @forelse($maintenances as $maintenance)
            {{-- {{ $loop->iteration == 6 && dd($loop) }} --}}
            <x-kanban-card-dropdown :model="$maintenance" :is-last="$isLast">
                <x-slot:trigger>
                    <div wire:key="maintenance-{{ $maintenance->id }}" class="!w-full">
                        <livewire:maintenances.kanban-card :maintenance="$maintenance" :key="$maintenance->id" />
                    </div>
                </x-slot:trigger>

                @if($this->canEdit())
                    <li>
                        <button wire:click="openEditDrawer('{{ $maintenance->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit
                        </button>
                    </li>
                @endif

                @if($this->canComplete($maintenance))
                    <li>
                        <button wire:click="openCompletedDrawer('{{ $maintenance->id }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded text-success">
                            <x-icon name="o-check-circle" class="w-4 h-4" />
                            Selesaikan
                        </button>
                    </li>
                @endif

                @if($this->canPrintReport())
                    <li>
                        <a href="{{ route('maintenances.pdf', $maintenance) }}" 
                           target="_blank" 
                           class="flex gap-2 items-center p-2 text-sm rounded">
                            <x-icon name="o-printer" class="w-4 h-4" />
                            Cetak Work Order
                        </a>
                    </li>
                @endif

                @foreach($this->getAvailableStatuses()->next as $availableStatus)
                    <li>
                        <button wire:click="moveToStatus('{{ $maintenance->id }}', '{{ $availableStatus->value }}')"
                            class="flex gap-2 items-center p-2 text-sm rounded text-{{ $availableStatus->color() }}">
                            <x-icon name="o-arrow-right" class="w-4 h-4" />
                            Move to {{ $availableStatus->label() }}
                        </button>
                    </li>
                @endforeach

                @if(count($this->getAvailableStatuses()->previous) > 0)
                    <div class="divider m-0"></div>
                    @foreach($this->getAvailableStatuses()->previous as $availableStatus)
                        <li>
                            <button wire:click="moveToStatus('{{ $maintenance->id }}', '{{ $availableStatus->value }}')"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-arrow-left" class="w-4 h-4" />
                                Move to {{ $availableStatus->label() }}
                            </button>
                        </li>
                    @endforeach
                @endif
            </x-kanban-card-dropdown>
        @empty
            <div class="flex flex-col gap-2 justify-center items-center py-8 text-center">
                <div class="p-3 rounded-full">
                    <x-icon name="o-inbox" class="w-6 h-6" />
                </div>
                <p class="text-sm opacity-70">Tidak ada item perawatan</p>
                <p class="text-xs opacity-50">Item akan muncul di sini ketika ditambahkan</p>
            </div>
        @endforelse
    </div>
</div>