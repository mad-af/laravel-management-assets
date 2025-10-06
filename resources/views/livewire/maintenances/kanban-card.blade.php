<div class="border shadow-sm transition-shadow cursor-pointer card bg-base-100 border-base-300 hover:shadow-md" >
    <div class="p-4 card-body">
        <!-- Priority and Type Badges -->
        <div class="flex justify-between items-start mb-2">
            <span class="badge badge-{{ $maintenance->priority->color() }} badge-xs">
                {{ $maintenance->priority->label() }}
            </span>
            <span class="badge badge-outline badge-{{ $maintenance->type->color() }} badge-xs">
                {{ $maintenance->type->label() }}
            </span>
        </div>

        <!-- Title -->
        <h4 class="mb-1 text-sm font-medium text-base-content line-clamp-2">
            {{ $maintenance->title }}
        </h4>

        <!-- Asset Info -->
        <div class="mb-1 text-xs text-base-content/70">
            <div class="flex gap-1 items-center">
                <x-icon name="o-cube" class="w-3 h-3" />
                <span>{{ $maintenance->asset->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Description -->
        @if($maintenance->description)
            <p class="mb-2 text-xs text-base-content/60 line-clamp-2">
                {{ Str::limit($maintenance->description, 80) }}
            </p>
        @endif

        <!-- Dates and Cost -->
        <div class="space-y-1 text-xs text-base-content/60">
            @if($maintenance->scheduled_date)
                <div class="flex gap-1 items-center">
                    <x-icon name="o-calendar" class="w-3 h-3" />
                    <span>{{ $maintenance->scheduled_date->format('d M Y') }}</span>
                </div>
            @endif
            @if($maintenance->completed_date)
                <div class="flex gap-1 items-center">
                    <x-icon name="o-check-circle" class="w-3 h-3" />
                    <span>Selesai: {{ $maintenance->completed_date->format('d M Y') }}</span>
                </div>
            @endif
            @if($maintenance->cost)
                <div class="flex gap-1 items-center">
                    {{-- <x-icon name="o-currency-dollar" class="w-3 h-3" /> --}}
                    <span>Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Assigned User -->
        @if($maintenance->assignedUser)
            <div class="flex gap-2 items-center pt-2 mt-3 border-t border-base-300">
                <div class="avatar placeholder">
                    <div class="w-6 h-6 rounded-full bg-neutral text-neutral-content">
                        <span class="text-xs">{{ substr($maintenance->assignedUser->name, 0, 1) }}</span>
                    </div>
                </div>
                <span class="text-xs text-base-content/70">{{ $maintenance->assignedUser->name }}</span>
            </div>
        @endif

        <!-- Notes Preview -->
        @if($maintenance->notes)
            <div class="pt-2 border-t border-base-300">
                <p class="text-xs italic text-base-content/60">
                    {{ Str::limit($maintenance->notes, 60) }}
                </p>
            </div>
        @endif
    </div>
</div>