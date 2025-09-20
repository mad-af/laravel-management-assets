<x-info-card title="History" icon="o-clock">
    {{-- <div class="overflow-x-auto"> --}}
        <div class="gap-1 min-w-max tabs tabs-box w-fit tabs-sm">
            <input type="radio" name="history_tabs" class="tab" aria-label="Odometer Log" wire:model.live="activeTab" value="odometer" />
            <input type="radio" name="history_tabs" class="tab" aria-label="Maintenance History" wire:model.live="activeTab" value="maintenance" />
        </div>
        <div class="mt-4" x-data="{ activeTab: @entangle('activeTab') }">
            <div x-show="activeTab === 'odometer'">
                @forelse($odometerLogs as $log)
                    <div class="p-4 rounded-lg border bg-base-100 border-base-300">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2 space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">
                                        {{ number_format($log->reading_km) }} km
                                    </span>
                                    <span class="text-sm text-base-content/60">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                @if($log->notes)
                                    <p class="p-2 mt-2 text-sm rounded text-base-content/80 bg-base-200/50">
                                        {{ $log->notes }}
                                    </p>
                                @endif
                            </div>
                            <div class="ml-4 text-right">
                                <span class="text-xs text-base-content/70">{{ $log->user->name }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-base-content/50">No odometer logs available</p>
                    </div>
                @endforelse
            </div>
            
            <div x-show="activeTab === 'maintenance'">
                @forelse($odometerLogs as $log)
                @empty
                <div class="py-8 text-center">
                    <p class="text-base-content/50">Maintenance history coming soon</p>
                </div>
                @endforelse
            </div>
        </div>
    {{-- </div> --}}
</x-info-card>