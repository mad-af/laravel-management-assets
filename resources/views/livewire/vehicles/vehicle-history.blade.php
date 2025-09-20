<x-info-card title="History" icon="o-clock">
    <div class="gap-1 tabs tabs-box tabs-sm w-fit mb-4">
        <button 
            wire:click="setActiveTab('odometer')" 
            class="tab {{ $activeTab === 'odometer' ? 'tab-active' : '' }}"
        >
            Odometer Log
        </button>
        <button 
            wire:click="setActiveTab('maintenance')" 
            class="tab {{ $activeTab === 'maintenance' ? 'tab-active' : '' }}"
        >
            Maintenance History
        </button>
    </div>

    <div class="tab-content">
        @if($activeTab === 'odometer')
            <div class="space-y-3">
                @forelse($odometerLogs as $log)
                    <div class="flex items-center justify-between p-3 border rounded-lg border-base-300">
                        <div>
                            <p class="font-medium">{{ number_format($log->reading_km) }} km</p>
                            <p class="text-sm text-base-content/70">{{ $log->created_at->format('d M Y, H:i') }}</p>
                            @if($log->notes)
                                <p class="text-sm text-base-content/60 mt-1">{{ $log->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-base-content/70">by {{ $log->user->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-base-content/50">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p>No odometer logs available</p>
                        </div>
                    </div>
                @endforelse
            </div>
        @elseif($activeTab === 'maintenance')
            <div class="text-center py-8">
                <div class="text-base-content/50">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p>Maintenance history coming soon</p>
                </div>
            </div>
        @endif
    </div>
</x-info-card>