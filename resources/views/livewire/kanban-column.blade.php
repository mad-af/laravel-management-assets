<div class="flex flex-col h-full">
    <!-- Column Header -->
    <div class="flex justify-between items-center p-4 py-2 border-b border-base-300 bg-base-100">
        <h3 class="text-sm font-semibold text-base-content">{{ $title }}</h3>
        <span class="w-6 text-center text-xs rounded-full {{ $this->getBadgeColorClass() }}">
            {{ $maintenances->count() }}
        </span>
    </div>

    <!-- Column Content -->
    <div class="overflow-y-auto flex-1 px-2 pt-4 space-y-2 rounded-lg border-dashed -2">
        @forelse($maintenances as $maintenance)
            <livewire:maintenance-card :maintenance="$maintenance" :key="'maintenance-'.$maintenance->id" />
        @empty
            <div class="flex flex-col gap-2 justify-center items-center py-8 text-center">
                <div class="p-3 rounded-full">
                    <x-icon name="o-inbox" class="w-6 h-6" />
                </div>
                <p class="text-sm opacity-70">No maintenance items</p>
                <p class="text-xs opacity-50">Items will appear here when added</p>
            </div>
        @endforelse
    </div>
</div>
