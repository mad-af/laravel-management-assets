<x-info-card title="Riwayat" icon="o-clock">
    {{-- <div class="overflow-x-auto"> --}}
        <div class="gap-1 min-w-max tabs tabs-box w-fit tabs-sm">
            <input type="radio" name="history_tabs" class="tab" aria-label="Log Odometer" wire:model.live="activeTab"
                value="odometer" />
            <input type="radio" name="history_tabs" class="tab" aria-label="Riwayat Perawatan"
                wire:model.live="activeTab" value="maintenance" />
        </div>
        <div class="mt-4" x-data="{ activeTab: @entangle('activeTab') }">
            <div x-show="activeTab === 'odometer'">
                @php
                    $headers = [
                        ['key' => 'read_at', 'label' => 'Tanggal & Waktu'],
                        ['key' => 'odometer_km', 'label' => 'Pembacaan (km)', 'class' => 'text-right'],
                        ['key' => 'source', 'label' => 'Sumber', 'class' => 'text-center'],
                        ['key' => 'notes', 'label' => 'Catatan']
                    ];
                @endphp
                <x-table :headers="$headers" :rows="$odometerLogs" class="table-sm">
                    @scope('cell_read_at', $log)
                    <div class="text-sm">
                        <div class="font-medium">{{ $log->read_at->format('d M Y') }}</div>
                        <div class="text-base-content/40">{{ $log->read_at->format('H:i') }}</div>
                    </div>
                    @endscope

                    @scope('cell_odometer_km', $log)
                    <span class="text-sm font-medium text-center">{{ number_format($log->odometer_km) }}</span>
                    @endscope

                    @scope('cell_source', $log)
                    <x-badge :value="$log->source->label()" class="badge-sm badge-{{ $log->source->color() }}" />
                    @endscope

                    @scope('cell_notes', $log)
                    <span class="text-sm text-base-content/40">{{ $log->notes ?: '-' }}</span>
                    @endscope
                </x-table>
            </div>

            <div x-show="activeTab === 'maintenance'">
                @php
                    $headers = [
                        ['key' => 'scheduled_date', 'label' => 'Tanggal Terjadwal'],
                        ['key' => 'type', 'label' => 'Jenis'],
                        ['key' => 'description', 'label' => 'Deskripsi'],
                        ['key' => 'cost', 'label' => 'Biaya'],
                        ['key' => 'status', 'label' => 'Status']
                    ];
                @endphp

                <x-table :headers="$headers" :rows="$maintenances" class="table-sm" showEmptyText>
                    @scope('cell_scheduled_date', $maintenance)
                    <div class="text-sm">
                        <div class="font-medium">
                            {{ $maintenance->started_at ? $maintenance->started_at->format('d M Y') : '-' }}
                        </div>
                        @if($maintenance->started_at)
                            <div class="text-base-content/40">{{ $maintenance->started_at->format('H:i') }}</div>
                        @endif
                    </div>
                    @endscope

                    @scope('cell_type', $maintenance)
                    <x-badge :value="$maintenance->type->label()"
                        class="badge-sm badge-outline badge-{{ $maintenance->type->color() }}" />
                    @endscope

                    @scope('cell_description', $maintenance)
                    <span class="text-sm">{{ $maintenance->title ?: '-' }}</span>
                    @endscope

                    @scope('cell_cost', $maintenance)
                    <span
                        class="font-mono text-sm">{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost) : '-' }}</span>
                    @endscope

                    @scope('cell_status', $maintenance)
                    <x-badge :value="$maintenance->status->label()"
                        class="badge-sm badge-{{ $maintenance->status->color() }}" />
                    @endscope
                </x-table>

            </div>
        </div>
        {{--
    </div> --}}
</x-info-card>