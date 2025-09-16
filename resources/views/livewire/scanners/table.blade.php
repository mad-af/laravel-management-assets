<div>
    <x-card title="Riwayat Scan Terakhir" class="shadow-xl">
        <x-slot:title>
            <div class="flex gap-2 items-center font-semibold">
                <x-icon name="o-clock" class="w-5 h-5 stroke-2" />
                <span>Riwayat Scan Terakhir</span>
            </div>
        </x-slot:title>

        <!-- Search and Actions -->
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search -->
            <div class="flex-1 max-w-md">
                <x-input wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan kode atau nama aset..."
                    icon="o-magnifying-glass" clearable />
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                @if($hasHistory)
                    <x-button wire:click="clearHistory" wire:confirm="Apakah Anda yakin ingin menghapus semua riwayat scan?"
                        outline class="btn-error">
                        <x-icon name="o-trash" class="w-4 h-4" />
                        Clear History
                    </x-button>
                @endif

                <x-button wire:click="$refresh" outline>
                    <x-icon name="o-arrow-path" class="w-4 h-4" />
                    Refresh
                </x-button>
            </div>
        </div>

        @if($hasHistory)
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kode</th>
                            <th>Nama Aset</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scanHistory as $scan)
                            <tr>
                                <td>
                                    <div class="text-sm">
                                        {{ \Carbon\Carbon::parse($scan['scanned_at'])->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-base-content/70">
                                        {{ \Carbon\Carbon::parse($scan['scanned_at'])->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-mono text-sm">{{ $scan['code'] }}</div>
                                </td>
                                <td>
                                    @if(isset($scan['asset']) && $scan['asset'])
                                        <div class="font-medium">{{ $scan['asset']['name'] }}</div>
                                        <div class="text-sm text-base-content/70">{{ $scan['asset']['code'] }}</div>
                                    @else
                                        <span class="italic text-base-content/50">Asset tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($scan['asset']) && $scan['asset'])
                                        @php
                                            $status = $scan['asset']['status'];
                                            $badgeClass = match ($status) {
                                                'available' => 'badge-success',
                                                'checked_out' => 'badge-warning',
                                                'maintenance' => 'badge-error',
                                                'retired' => 'badge-neutral',
                                                default => 'badge-ghost'
                                            };
                                        @endphp
                                        <x-badge :value="ucfirst(str_replace('_', ' ', $status))" class="{{ $badgeClass }}" />
                                    @else
                                        <x-badge value="Not Found" class="badge-ghost" />
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-center">
                                        @if(isset($scan['asset']) && $scan['asset'])
                                            <x-button wire:click="viewAsset({{ $scan['asset']['id'] }})" size="xs" outline
                                                title="View Asset">
                                                <x-icon name="o-eye" class="w-3 h-3" />
                                            </x-button>
                                        @endif

                                        <x-button wire:click="rescanCode('{{ $scan['code'] }}')" size="xs" outline
                                            title="Scan Ulang">
                                            <x-icon name="o-arrow-path" class="w-3 h-3" />
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-base-content/70">
                                    @if($search)
                                        <div class="space-y-2">
                                            <x-icon name="o-magnifying-glass" class="mx-auto w-12 h-12 text-base-300" />
                                            <p>Tidak ada hasil yang ditemukan untuk "{{ $search }}"</p>
                                            <x-button wire:click="$set('search', '')" size="sm" outline>
                                                Clear Search
                                            </x-button>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            <x-icon name="o-clock" class="mx-auto w-12 h-12 text-base-300" />
                                            <p>Belum ada riwayat scan</p>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            @if($total > 10)
                <div class="flex justify-between items-center mt-4">
                    <div class="text-sm text-base-content/70">
                        Menampilkan {{ $scanHistory->count() }} dari {{ $total }} hasil
                    </div>

                    {{ $this->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="py-12 text-center">
                <x-icon name="o-clock" class="mx-auto mb-4 w-16 h-16 text-base-300" />
                <h3 class="mb-2 text-lg font-medium text-base-content/70">Belum ada riwayat scan</h3>
                <p class="text-sm text-base-content/50">Mulai scan QR/Barcode untuk melihat riwayat di sini</p>
            </div>
        @endif
    </x-card>
</div>