<x-info-card title="Riwayat Perawatan Aset" icon="o-wrench-screwdriver">
    <div class="mb-4">
        <x-button wire:click="toggleShowAll" class="btn-sm">
            {{ $showAll ? 'Tampilkan 10 Terakhir' : 'Tampilkan Semua' }}
        </x-button>
    </div>

    @php
        $headers = [
            ['key' => 'time', 'label' => 'Waktu'],
            ['key' => 'title', 'label' => 'Judul'],
            ['key' => 'type', 'label' => 'Tipe'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'priority', 'label' => 'Prioritas'],
            ['key' => 'assignee', 'label' => 'PIC'],
        ];
    @endphp

    <x-table :headers="$headers" :rows="$maintenances" wire:model="expanded" class="table-sm" striped expandable
        showEmptyText>
        @scope('cell_time', $m)
        @php
            $date = $m->started_at ?? $m->created_at;
        @endphp
        <div class="text-sm">
            <div class="font-medium">{{ $date?->format('d M Y') }}</div>
            <div class="text-xs text-base-content/60">{{ $date?->format('H:i') }}</div>
        </div>
        @endscope

        @scope('cell_title', $m)
        <div class="flex flex-col">
            <div class="font-medium">{{ $m->title }}</div>
            <div class="text-xs whitespace-nowrap text-base-content/60">{{ $m->code ?? '-' }}</div>
        </div>
        @endscope

        @scope('cell_type', $m)
        <x-badge value="{{ $m->type->label() }}" class="badge-outline badge-{{ $m->type->color() }} badge-sm" />
        @endscope

        @scope('cell_status', $m)
        <x-badge value="{{ $m->status->label() }}" class="badge-{{ $m->status->color() }} badge-sm" />
        @endscope

        @scope('cell_priority', $m)
        <x-badge value="{{ $m->priority->label() }}" class="badge-outline badge-{{ $m->priority->color() }} badge-sm" />
        @endscope

        @scope('cell_assignee', $m)
        @php
            $name = $m->employee?->full_name ?? $m->technician_name ?? $m->vendor_name ?? null;
        @endphp
        <div class="tooltip">
            <div class="text-xs tooltip-content">
                <div class="font-medium">{{ $name ?? '-' }}</div>
            </div>
            <x-avatar placeholder="{{ strtoupper(substr($name ?? '-', 0, 2)) }}"
                class="!w-9 !bg-primary !font-bold border-2 border-base-100" />
        </div>
        @endscope

        @scope('expansion', $m)
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="space-y-1">
                <h3 class="font-bold">Jadwal</h3>
                <div class="p-2 text-sm rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                    @if($m->started_at)
                        <div>Mulai: {{ $m->started_at->format('d M Y H:i') }}</div>
                    @endif
                    @if($m->estimated_completed_at)
                        <div>Estimasi: {{ $m->estimated_completed_at->format('d M Y H:i') }}</div>
                    @endif
                    @if($m->completed_at)
                        <div>Selesai: {{ $m->completed_at->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold">Detail</h3>
                <div class="p-2 text-sm rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                    <div>Vendor: {{ $m->vendor_name ?? '-' }}</div>
                    <div>Teknisi: {{ $m->technician_name ?? '-' }}</div>
                    <div>Odometer:
                        {{ $m->odometer_km_at_service ? number_format($m->odometer_km_at_service, 0, ',', '.') . ' km' : '-' }}
                    </div>
                    <div>Biaya: {{ $m->cost ? 'Rp ' . number_format($m->cost, 0, ',', '.') : '-' }}</div>
                    <div>Invoice: {{ $m->invoice_no ?? '-' }}</div>
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold">Catatan</h3>
                <div class="p-2 text-sm rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                    @if($m->notes)
                        <p>{{ $m->notes }}</p>
                    @else
                        <span class="text-base-content/50">-</span>
                    @endif
                </div>
            </div>
            @if((is_array($m->service_tasks) && count($m->service_tasks) > 0) || (is_array($m->service_details) && count($m->service_details) > 0))
                <div class="space-y-1 md:col-span-3">
                    <h3 class="font-bold">Ringkasan Servis</h3>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        @if(is_array($m->service_tasks) && count($m->service_tasks) > 0)
                            <div class="p-2 text-sm rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                                <h4 class="mb-1 font-semibold">Tugas</h4>
                                <ul class="pl-5 list-disc">
                                    @foreach($m->service_tasks as $task)
                                        @php
                                            $taskText = is_array($task) ? ($task['task'] ?? json_encode($task)) : $task;
                                            $isCompleted = is_array($task) ? ($task['completed'] ?? null) : null;
                                        @endphp
                                        <li class="{{ $isCompleted === true ? 'line-through text-base-content/60' : '' }}">
                                            {{ $taskText }}
                                            @if($isCompleted === true)
                                                <x-icon name="o-check" class="inline w-3 h-3 text-success" />
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(is_array($m->service_details) && count($m->service_details) > 0)
                            <div class="p-2 text-sm rounded border border-base-300/80 bg-base-200/60 text-base-content/80">
                                <h4 class="mb-1 font-semibold">Detail</h4>
                                <ul class="pl-5 list-disc">
                                    @foreach($m->service_details as $item)
                                        @php
                                            $name = is_array($item) ? ($item['name'] ?? null) : null;
                                            $qty = is_array($item) ? ($item['qty'] ?? null) : null;
                                        @endphp
                                        @if($name)
                                            <li>
                                                {{ $name }}
                                                @if(!is_null($qty))
                                                    <span class="text-xs text-base-content/60">× {{ $qty }}</span>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                        @endif
                    </div>
                </div>
            @endif
        </div>
        @endscope
    </x-table>

    @if($showAll && method_exists($maintenances, 'links'))
        <div class="mt-4">
            {{ $maintenances->links() }}
        </div>
    @endif
</x-info-card>