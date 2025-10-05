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
            <livewire:maintenances.kanban-card :maintenance="$maintenance" :key="$maintenance->id" />
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