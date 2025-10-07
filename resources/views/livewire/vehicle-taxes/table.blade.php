<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        {{-- Status Taxes Tabs --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                @php
                    $statusTabs = [
                        [
                            'value' => 'overdue',
                            'label' => 'Terlambat',
                            'count' => $this->overdueCount,
                            'badge_class' => 'badge-sm badge-error',
                        ],
                        [
                            'value' => 'due_soon',
                            'label' => 'Jatuh Tempo',
                            'count' => $this->dueSoonCount,
                            'badge_class' => 'badge-sm badge-warning',
                        ],
                        [
                            'value' => 'paid',
                            'label' => 'Dibayar',
                            'count' => $this->paidCount,
                            'badge_class' => 'badge-sm badge-success',
                        ],
                        [
                            'value' => 'not_valid',
                            'label' => 'Belum Valid',
                            'count' => $this->notValidCount,
                            'badge_class' => 'badge-sm badge-ghost badge-soft',
                        ],
                    ];
                @endphp

                @foreach($statusTabs as $tab)
                    <label class="gap-2 tab">
                        <input type="radio" name="status_tabs" class="checked:bg-base-100 checked:shadow"
                            wire:model.live="statusFilter" value="{{ $tab['value'] }}" />
                        {{ $tab['label'] }}
                        <x-badge class="{{ $tab['badge_class'] }}" value="{{ $tab['count'] }}" />
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Header with Search --}}
        <div class="flex flex-col gap-4 mb-4 sm:flex-row">
            <div class="flex-1">
                <x-input wire:model.live="search" placeholder="Cari kendaraan (nama, kode, plat nomor)..."
                    icon="o-magnifying-glass" class="input-sm" />
            </div>
        </div>

        {{-- Table --}}
        <div>
            @php
                $headers = [
                    ['key' => 'vehicle_info', 'label' => 'Kendaraan'],
                    ['key' => 'plate_no', 'label' => 'Plat Nomor'],
                    ['key' => 'last_tax_types', 'label' => 'Jenis Pajak'],
                    ['key' => 'last_payment', 'label' => 'Pembayaran Terakhir'],
                    ['key' => 'payment_count', 'label' => 'Jumlah Pembayaran'],
                    ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-24'],
                ];
            @endphp

            <x-table :headers="$headers" :rows="$vehicleAssets" striped show-empty-text>
                @scope('cell_vehicle_info', $vehicle)
                <div class="flex gap-2 items-center">
                    @if (!$vehicle->image)
                        <div
                            class="flex justify-center items-center font-bold rounded-lg border-2 size-13 bg-base-300 border-base-100">
                            <x-icon name="o-photo" class="w-6 h-6 text-base-content/60" />
                        </div>
                    @else
                        <x-avatar :image="asset('storage/' . $vehicle->image)"
                            class="!w-13 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100">
                        </x-avatar>
                    @endif
                    <div>
                        <div class="font-mono text-xs truncate text-base-content/60">{{ $vehicle->code }}</div>
                        <div class="font-medium">{{ $vehicle->name }}</div>
                        <div class="text-xs text-base-content/60">Tag: {{ $vehicle->tag_code }}</div>
                    </div>
                </div>
                @endscope

                @scope('cell_plate_no', $vehicle)
                <span class="font-mono font-medium">{{ $vehicle->vehicleProfile?->plate_no ?? '—' }}</span>
                @endscope

                @scope('cell_last_tax_types', $vehicle)
                @php
                    $taxTypes = $vehicle->vehicleTaxTypes;
                @endphp
                @if($taxTypes->count() > 0)
                    <div class="flex flex-col gap-1">
                        @foreach($taxTypes->take(3) as $taxType)
                            @php
                                $dueDate = \Carbon\Carbon::parse($taxType->due_date);
                                $paidHistory = $vehicle->vehicleTaxHistories->where('vehicle_tax_type_id', $taxType->id)->first();

                                if ($paidHistory) {
                                    $status = 'paid';
                                    $statusClass = 'badge-success';
                                    $statusText = 'Dibayar';
                                } elseif ($dueDate->isPast()) {
                                    $status = 'overdue';
                                    $statusClass = 'badge-error';
                                    $statusText = 'Terlambat';
                                } elseif ($dueDate->diffInMonths(now()) <= 3) {
                                    $status = 'due_soon';
                                    $statusClass = 'badge-warning';
                                    $statusText = 'Jatuh Tempo';
                                } else {
                                    $status = 'upcoming';
                                    $statusClass = 'badge-info';
                                    $statusText = 'Akan Datang';
                                }
                            @endphp
                            <div class="flex gap-2 justify-between items-center">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">{{$taxType->tax_type->label() }}</div>
                                </div>
                                <x-badge class="badge-xs {{ $statusClass }}" value="{{ $statusText }}" />
                            </div>
                        @endforeach
                        @if($taxTypes->count() > 3)
                            <div class="mt-1 text-xs text-base-content/50">
                                +{{ $taxTypes->count() - 3 }} lainnya
                            </div>
                        @endif
                    </div>
                @else
                    <span class="text-base-content/50">—</span>
                @endif
                @endscope

                @scope('cell_last_payment', $vehicle)
                @php
                    $lastPayment = $vehicle->vehicleTaxHistories->sortByDesc('paid_date')->first();
                @endphp
                @if($lastPayment)
                    <div class="flex flex-col">
                        <span class="text-sm">{{ \Carbon\Carbon::parse($lastPayment->paid_date)->format('d M Y') }}</span>
                        <span class="text-xs text-base-content/70">Rp
                            {{ number_format($lastPayment->amount, 0, ',', '.') }}</span>
                    </div>
                @else
                    <span class="text-base-content/50">—</span>
                @endif
                @endscope

                @scope('cell_payment_count', $vehicle)
                @php
                    $paidCount = $vehicle->vehicleTaxHistories->count();
                    $totalTaxTypes = $vehicle->vehicleTaxTypes->count();
                @endphp
                <div class="flex flex-col">
                    <span class="text-sm font-medium">{{ $paidCount }}</span>
                    <span class="text-xs text-base-content/70">dari {{ $totalTaxTypes }} pajak</span>
                </div>
                @endscope

                @scope('cell_actions', $vehicle)
                <x-action-dropdown :model="$vehicle" id="dropdown-menu-{{ $vehicle->id }}">
                    @if ($this->statusFilter === 'not_valid')
                        <li>
                            <button wire:click="$dispatch('open-tax-type-drawer', { assetId: '{{ $vehicle->id }}' })"
                                class="flex gap-2 items-center p-2 w-full text-sm text-left rounded">
                                <x-icon name="o-document" class="w-4 h-4" />
                                Konfigurasi Pajak
                            </button>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('assets.show', $vehicle->id) }}"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-eye" class="w-4 h-4" />
                                Lihat Detail
                            </a>
                        </li>
                    @endif
                </x-action-dropdown>
                @endscope
            </x-table>
        </div>

        {{-- Pagination Info --}}
        @if($vehicleAssets->count() > 0)
            <div class="flex flex-col gap-4 mt-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="text-sm text-base-content/70">
                    Menampilkan {{ $vehicleAssets->firstItem() }}–{{ $vehicleAssets->lastItem() }} dari
                    {{ $vehicleAssets->total() }} kendaraan
                </div>
                <div class="mt-4">
                    {{ $vehicleAssets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>