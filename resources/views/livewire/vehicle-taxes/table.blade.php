<div class="shadow card bg-base-100">
    <div class="space-y-4 card-body">
        {{-- Status Taxes Tabs --}}
        <div class="overflow-x-auto">
            <div class="gap-1 items-center min-w-max tabs tabs-box tabs-sm w-fit">
                @foreach($this->getStatusTabs() as $tab)
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
            <x-table :headers="$headers" :rows="$vehicleAssets" wire:model="expanded" striped show-empty-text
                expandable>
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
                    // Ambil koleksi tax histories yang sudah diurutkan
                    $taxHistories = $this->getSortedTaxHistories($vehicle);
                @endphp
                @if($taxHistories->count() > 0)
                    <div class="flex flex-col gap-1">
                        @foreach($taxHistories->take(3) as $taxHistory)
                            @php
                                $taxStatus = $this->getTaxHistoryStatus($taxHistory);
                            @endphp
                            <div class="flex gap-2 justify-between items-center bg-base-300/60">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium truncate">{{$taxHistory->vehicleTaxType->tax_type->label() }} ({{ $taxHistory->year }})</div>
                                </div>
                                <x-badge class="badge-xs {{ $taxStatus['statusClass'] }}"
                                    value="{{ $taxStatus['statusText'] }}" />
                            </div>
                        @endforeach
                        @if($taxHistories->count() > 3)
                            <div class="mt-1 text-xs text-base-content/50">
                                +{{ $taxHistories->count() - 3 }} lainnya
                            </div>
                        @endif
                    </div>
                @else
                    <span class="text-base-content/50">—</span>
                @endif
                @endscope

                @scope('cell_last_payment', $vehicle)
                @php
                    $lastPayment = $this->getLastPayment($vehicle);
                @endphp
                @if($lastPayment?->paid_date)
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
                    $paymentData = $this->getPaymentCount($vehicle);
                @endphp
                <div class="flex flex-col">
                    <span class="text-sm font-medium">{{ $paymentData['paid_count'] }}</span>
                    <span class="text-xs text-base-content/70">dari {{ $paymentData['total_tax_types'] }} pajak</span>
                </div>
                @endscope

                @scope('cell_actions', $vehicle)
                <x-action-dropdown :model="$vehicle" id="dropdown-menu-{{ $vehicle->id }}">
                    @if ($this->statusFilter === 'not_valid')
                        <li>
                            <button wire:click="$dispatch('open-tax-type-drawer', { assetId: '{{ $vehicle->id }}' })"
                                class="flex gap-2 items-center p-2 w-full text-sm text-left rounded">
                                <x-icon name="o-document" class="w-4 h-4" />
                                Konfigurasi Pajak Kendaraan
                            </button>
                        </li>
                    @else
                        @if ($this->statusFilter != 'paid')
                            <li>
                                <button wire:click="$dispatch('open-drawer', { assetId: '{{ $vehicle->id }}' })"
                                    class="flex gap-2 items-center p-2 text-sm rounded text-primary">
                                    <x-icon name="o-calculator" class="w-4 h-4" />
                                    Bayar Pajak
                                </button>
                            </li>
                        @endif
                        <li class="opacity-50 cursor-not-allowed pointer-events-none">
                            <a href="{{ route('assets.show', $vehicle->id) }}"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-eye" class="w-4 h-4" />
                                Lihat Detail
                            </a>
                        </li>
                    @endif
                </x-action-dropdown>
                @endscope

                @scope('expansion', $vehicle)
                @php
                    // Ambil koleksi tax histories yang sudah diurutkan
                    $taxHistories = $this->getSortedTaxHistories($vehicle);


                    $taxTypeHeaders = [
                        ['key' => 'tax_type', 'label' => 'Jenis Pajak'],
                        ['key' => 'year', 'label' => 'Tahun'],
                        ['key' => 'due_date', 'label' => 'Jatuh Tempo'],
                        ['key' => 'status', 'label' => 'Status'],
                        ['key' => 'last_payment', 'label' => 'Pembayaran Terakhir'],
                    ];
                @endphp

                <div class="text-sm font-semibold">
                    Pajak ({{ count($taxHistories) }})
                </div>

                <x-table :headers="$taxTypeHeaders" :rows="$taxHistories" no-headers no-hover show-empty-text>
                    @scope('cell_tax_type', $taxHistory)
                    <div class="flex flex-col">
                        <span class="font-medium">{{ $taxHistory->vehicleTaxType->tax_type->label() }}</span>
                        <span
                            class="text-xs text-base-content/60">{{ $taxHistory->vehicleTaxType->tax_type->description() }}</span>
                    </div>
                    @endscope

                    @scope('cell_due_date', $taxHistory)
                    <span class="text-sm">
                        {{ \Carbon\Carbon::parse($taxHistory->due_date)->format('d M Y') }}
                    </span>
                    @endscope

                    @scope('cell_status', $taxHistory)
                    @php
                        $dueDate = \Carbon\Carbon::parse($taxHistory->due_date);
                        if ($taxHistory->paid_date) {
                            $taxStatus = [
                                'statusClass' => 'badge-success',
                                'statusText' => 'Dibayar'
                            ];
                        } elseif ($dueDate->isPast()) {
                            $taxStatus = [
                                'statusClass' => 'badge-error',
                                'statusText' => 'Terlambat'
                            ];
                        } elseif ($dueDate->isFuture()) {
                            $taxStatus = [
                                'statusClass' => 'badge-warning',
                                'statusText' => 'Jatuh Tempo'
                            ];
                        } else {
                            $taxStatus = [
                                'statusClass' => 'badge-info',
                                'statusText' => 'Akan Datang'
                            ];
                        }
                    @endphp
                    <x-badge class="badge-xs {{ $taxStatus['statusClass'] }}" value="{{ $taxStatus['statusText'] }}" />
                    @endscope

                    @scope('cell_last_payment', $taxHistory)
                    @if($taxHistory->paid_date)
                        <div class="flex flex-col">
                            <span
                                class="text-sm">{{ \Carbon\Carbon::parse($taxHistory->paid_date)->format('d M Y') }}</span>
                            @if($taxHistory->amount)
                                <span class="font-mono text-xs text-base-content/60">
                                    Rp {{ number_format($taxHistory->amount, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-base-content/50">Belum ada pembayaran</span>
                    @endif
                    @endscope
                </x-table>
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