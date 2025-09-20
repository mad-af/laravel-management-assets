<x-info-card title="History" icon="o-clock">
    {{-- <div class="overflow-x-auto"> --}}
        <div class="gap-1 min-w-max tabs tabs-box w-fit tabs-sm">
            <input type="radio" name="history_tabs" class="tab" aria-label="Odometer Log" wire:model.live="activeTab" value="odometer" />
            <input type="radio" name="history_tabs" class="tab" aria-label="Maintenance History" wire:model.live="activeTab" value="maintenance" />
        </div>
        <div class="mt-4" x-data="{ activeTab: @entangle('activeTab') }">
            <div x-show="activeTab === 'odometer'">
                @if($odometerLogs->isEmpty())
                    <div class="py-8 text-center">
                        <p class="text-base-content/50">No odometer logs available</p>
                    </div>
                @else
                    <x-table :headers="[
                        ['key' => 'reading_km', 'label' => 'Reading (km)', 'class' => 'w-32'],
                        ['key' => 'read_at', 'label' => 'Date & Time', 'class' => 'w-40'],
                        ['key' => 'source', 'label' => 'Source', 'class' => 'w-24'],
                        ['key' => 'notes', 'label' => 'Notes', 'class' => 'w-auto']
                    ]" :rows="$odometerLogs" class="table-sm">
                        @scope('cell_reading_km', $log)
                            <span class="font-mono font-medium">{{ number_format($log->reading_km) }}</span>
                        @endscope
                        
                        @scope('cell_read_at', $log)
                            <div class="text-sm">
                                <div class="font-medium">{{ $log->read_at->format('d M Y') }}</div>
                                <div class="text-gray-500">{{ $log->read_at->format('H:i') }}</div>
                            </div>
                        @endscope
                        
                        @scope('cell_source', $log)
                             <x-badge :value="$log->source->label()" class="badge-sm"
                                 :class="[
                                     'badge-primary' => $log->source->value === 'manual',
                                     'badge-info' => $log->source->value === 'telematics', 
                                     'badge-success' => $log->source->value === 'service'
                                 ][$log->source->value] ?? 'badge-ghost'" />
                         @endscope
                        
                        @scope('cell_notes', $log)
                            <span class="text-sm text-gray-600">{{ $log->notes ?: '-' }}</span>
                        @endscope
                    </x-table>
                @endif
            </div>
            
            <div x-show="activeTab === 'maintenance'">
                @if($maintenances->isEmpty())
                    <div class="py-8 text-center">
                        <p class="text-base-content/50">No maintenance records available</p>
                    </div>
                @else
                    <x-table :headers="[
                        ['key' => 'scheduled_date', 'label' => 'Scheduled Date', 'class' => 'w-32'],
                        ['key' => 'type', 'label' => 'Type', 'class' => 'w-24'],
                        ['key' => 'description', 'label' => 'Description', 'class' => 'w-auto'],
                        ['key' => 'cost', 'label' => 'Cost', 'class' => 'w-28'],
                        ['key' => 'status', 'label' => 'Status', 'class' => 'w-24']
                    ]" :rows="$maintenances" class="table-sm">
                        @scope('cell_scheduled_date', $maintenance)
                            <span class="text-sm font-medium">{{ $maintenance->scheduled_date->format('d M Y') }}</span>
                        @endscope
                        
                        @scope('cell_type', $maintenance)
                            <x-badge :value="ucfirst($maintenance->type)" class="badge-sm badge-outline" />
                        @endscope
                        
                        @scope('cell_description', $maintenance)
                            <span class="text-sm">{{ $maintenance->description ?: '-' }}</span>
                        @endscope
                        
                        @scope('cell_cost', $maintenance)
                            <span class="font-mono text-sm">{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost) : '-' }}</span>
                        @endscope
                        
                        @scope('cell_status', $maintenance)
                            <x-badge :value="ucfirst($maintenance->status)" class="badge-sm"
                                :class="[
                                    'badge-warning' => $maintenance->status === 'scheduled',
                                    'badge-info' => $maintenance->status === 'in_progress',
                                    'badge-success' => $maintenance->status === 'completed',
                                    'badge-error' => $maintenance->status === 'cancelled'
                                ][$maintenance->status] ?? 'badge-ghost'" />
                        @endscope
                    </x-table>
                @endif
            </div>
        </div>
    {{-- </div> --}}
</x-info-card>