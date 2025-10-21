<x-info-card title="Pajak Kendaraan: Terlambat & Jatuh Tempo" icon="o-receipt-refund">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Terlambat</h3>
                <span class="badge badge-error">{{ $overdueCount }}</span>
            </div>
            @if($overdueCount === 0)
                <div class="alert alert-success">
                    <x-icon name="o-check-circle" class="w-5 h-5" />
                    <span>Tidak ada pajak terlambat.</span>
                </div>
            @else
                <ul class="menu bg-base-200 rounded-box">
                    @foreach($overdue as $v)
                        <li>
                            <span>
                                <x-icon name="o-exclamation-triangle" class="w-4 h-4 text-error" />
                                <span class="font-medium">{{ $v->name }}</span>
                                <span class="opacity-70">• Due: {{ optional($v->vehicleTaxHistories()->latest('due_date')->first())->due_date?->format('d M Y') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold">Jatuh Tempo</h3>
                <span class="badge badge-warning">{{ $dueSoonCount }}</span>
            </div>
            @if($dueSoonCount === 0)
                <div class="alert alert-info">
                    <x-icon name="o-information-circle" class="w-5 h-5" />
                    <span>Tidak ada pajak yang akan jatuh tempo.</span>
                </div>
            @else
                <ul class="menu bg-base-200 rounded-box">
                    @foreach($dueSoon as $v)
                        <li>
                            <span>
                                <x-icon name="o-exclamation-circle" class="w-4 h-4 text-warning" />
                                <span class="font-medium">{{ $v->name }}</span>
                                <span class="opacity-70">• Due: {{ optional($v->vehicleTaxHistories()->latest('due_date')->first())->due_date?->format('d M Y') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-info-card>