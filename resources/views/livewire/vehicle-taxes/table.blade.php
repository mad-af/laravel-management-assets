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
                    <label class="tab gap-2">
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
                    ['key' => 'last_due_date', 'label' => 'Jatuh Tempo Terakhir'],
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

                @scope('cell_last_due_date', $vehicle)
                @php
                    $annualTaxDueDate = $vehicle->vehicleProfile?->annual_tax_due_date;
                @endphp
                @if($annualTaxDueDate)
                    <div class="flex flex-col">
                        <span class="text-sm">{{ \Carbon\Carbon::parse($annualTaxDueDate)->format('d M Y') }}</span>
                        @php
                            $dueDate = \Carbon\Carbon::parse($annualTaxDueDate);
                        @endphp
                        @if($vehicle->vehicleTaxes->where('due_date', $annualTaxDueDate)->whereNotNull('payment_date')->isNotEmpty())
                            <span class="text-xs text-success">Sudah dibayar</span>
                        @elseif($dueDate->isPast())
                            <span class="text-xs text-error">Terlambat {{ $dueDate->diffForHumans(['parts' => 2, 'join' => ' ']) }}</span>
                        @else
                            <span class="text-xs text-warning">{{ $dueDate->diffForHumans(['parts' => 2, 'join' => ' ']) }}</span>
                        @endif
                    </div>
                @else
                    <span class="text-base-content/50">—</span>
                @endif
                @endscope

                @scope('cell_last_payment', $vehicle)
                @php
                    $lastPayment = $vehicle->vehicleTaxes->where('payment_date', '!=', null)->first();
                @endphp
                @if($lastPayment)
                    <div class="flex flex-col">
                        <span class="text-sm">{{ $lastPayment->payment_date->format('d M Y') }}</span>
                        <span class="text-xs text-base-content/70">Rp
                            {{ number_format($lastPayment->amount, 0, ',', '.') }}</span>
                    </div>
                @else
                    <span class="text-base-content/50">—</span>
                @endif
                @endscope

                @scope('cell_payment_count', $vehicle)
                @php
                    // $paidCount = $vehicle->vehicleTaxes->where('payment_date', '!=', null)->count();
                    $totalCount = $vehicle->vehicleTaxes->count();
                @endphp
                <div class="flex flex-col">
                    <span class="text-sm font-medium">{{ $totalCount }}</span>
                    {{-- <span class="text-xs text-base-content/70">pembayaran</span> --}}
                </div>
                @endscope

                @scope('cell_actions', $vehicle)
                <x-action-dropdown :model="$vehicle" id="dropdown-menu-{{ $vehicle->id }}">
                    @if ($this->statusFilter === 'not_valid')
                        <li>
                            <a href="/admin/vehicles?action=save-profile&asset_id={{ $vehicle->id }}"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-truck" class="w-4 h-4" />
                                Lengkapi Profil Kendaraan
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('vehicles.show', $vehicle->id) }}"
                                class="flex gap-2 items-center p-2 text-sm rounded">
                                <x-icon name="o-eye" class="w-4 h-4" />
                                Lihat Detail
                            </a>
                        </li>
                        <li>
                            <button wire:click="delete('{{ $vehicle->id }}')"
                                wire:confirm="Apakah Anda yakin ingin menghapus kendaraan ini?"
                                class="flex gap-2 items-center p-2 text-sm rounded text-error">
                                <x-icon name="o-trash" class="w-4 h-4" />
                                Hapus
                            </button>
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