<div>
    <!-- Scanner Interface -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Camera Scanner -->
        <x-card class="shadow-sm">
            <x-slot:title>
                <div class="flex gap-2 items-center font-semibold">
                    <x-icon name="o-camera" class="w-5 h-5 stroke-2" />
                    <span>Scanner Kamera</span>
                </div>
            </x-slot:title>

            <!-- Camera Preview -->
            <div class="overflow-hidden relative rounded-lg bg-base-200" style="aspect-ratio: 4/3;">
                <video id="scanner-video" class="object-cover w-full h-full" autoplay muted playsinline></video>
                <div id="scanner-overlay" class="flex absolute inset-0 justify-center items-center">
                    <div class="rounded-lg border-2 border-dashed border-primary" style="width: 250px; height: 250px;">
                        <div class="flex justify-center items-center w-full h-full text-primary">
                            <div class="text-center">
                                <x-icon name="o-qr-code" class="mx-auto mb-2 w-12 h-12" />
                                <p class="text-sm">Arahkan kamera ke QR/Barcode</p>
                            </div>
                        </div>
                    </div>
                </div>
                <canvas id="scanner-canvas" class="hidden"></canvas>
            </div>

            <!-- Scanner Controls -->
            <div class="flex gap-2 mt-4">
                <button id="start-scanner" class="flex-1 btn btn-primary">
                    <x-icon name="o-play" class="w-4 h-4" />
                    Mulai Scan
                </button>
                <button id="stop-scanner" class="flex-1 btn btn-outline" disabled>
                    <x-icon name="o-stop" class="w-4 h-4" />
                    Stop Scan
                </button>
                <button id="switch-camera" class="btn btn-ghost">
                    <x-icon name="o-arrow-path" class="w-4 h-4" />
                </button>
            </div>

            <!-- Scanner Status -->
            <div id="scanner-status" class="mt-4">
                @if($isScanning)
                    <x-alert icon="o-qr-code" class="alert-success">
                        Scanner aktif - Arahkan kamera ke QR/Barcode
                    </x-alert>
                @else
                    <x-alert icon="o-information-circle" class="alert-info">
                        Klik "Mulai Scan" untuk mengaktifkan kamera
                    </x-alert>
                @endif
            </div>
        </x-card>

        <!-- Scan Results -->
        <x-card title="Hasil Scan" class="shadow-xl">
            <x-slot:title>
                <div class="flex gap-2 items-center font-semibold">
                    <x-icon name="o-magnifying-glass" class="w-5 h-5 stroke-2" />
                    <span>Hasil Scan</span>
                </div>
            </x-slot:title>

            @if($scannedCode)
                <!-- Scan Result Display -->
                <div class="mb-4">
                    <div class="p-4 mb-4 rounded-lg bg-base-200">
                        <div class="flex gap-2 items-center mb-2">
                            <x-icon name="o-qr-code" class="w-4 h-4 text-success" />
                            <span class="font-semibold">Kode Terdeteksi:</span>
                        </div>
                        <div class="p-2 font-mono text-sm rounded bg-base-300">{{ $scannedCode }}</div>
                    </div>

                    @if($asset)
                        <!-- Asset Information -->
                        <div class="space-y-4">
                            <x-header title="Informasi Aset" size="text-lg" />

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-semibold">Nama Aset:</span>
                                    <span>{{ $asset->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Kode Aset:</span>
                                    <span>{{ $asset->code }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Kategori:</span>
                                    <span>{{ $asset->category->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Lokasi:</span>
                                    <span>{{ $asset->location->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Status:</span>
                                    <x-badge :value="$asset->status->label()"
                                        class="badge-{{ $asset->status->value === 'available' ? 'success' : ($asset->status->value === 'checked_out' ? 'warning' : 'error') }}" />
                                </div>
                            </div>

                            <x-header title="Quick Actions" size="text-lg" />

                            <!-- Asset Actions -->
                            <div class="space-y-3">
                                @if($asset->status->value !== 'checked_out')
                                    <!-- Maintenance and Checkout buttons side by side -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <button wire:click="updateAssetStatus('maintenance')" outline>
                                            <x-icon name="o-cog-6-tooth" class="mr-2 w-4 h-4" />
                                            Maintenance
                                        </button>
                                        <button wire:click="openCheckoutDrawer" class="btn-primary">
                                            <x-icon name="o-arrow-up-tray" class="mr-2 w-4 h-4" />
                                            Check Out
                                        </button>
                                    </div>
                                @else
                                    <!-- Check In button -->
                                    <button wire:click="openCheckinDrawer" class="w-full btn-success">
                                        <x-icon name="o-arrow-down-tray" class="mr-2 w-4 h-4" />
                                        Check In
                                    </button>
                                @endif

                                <!-- View Detail button -->
                                <button wire:click="viewAssetDetail" class="w-full" outline>
                                    <x-icon name="o-eye" class="mr-2 w-4 h-4" />
                                    View Detail
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Not Found Message -->
                        <x-alert icon="o-exclamation-triangle" class="alert-warning">
                            <x-slot:title>Aset Tidak Ditemukan</x-slot:title>
                            Kode yang dipindai tidak terdaftar dalam sistem.
                        </x-alert>
                    @endif
                </div>

                <!-- Clear Result Button -->
                <div class="mt-4">
                    <button wire:click="clearResult" outline class="w-full">
                        <x-icon name="o-x-mark" class="mr-2 w-4 h-4" />
                        Clear Result
                    </button>
                </div>
            @else
                <!-- Empty State -->
                <div class="py-8 text-center">
                    <x-icon name="o-qr-code" class="mx-auto mb-4 w-16 h-16 text-base-300" />
                    <p class="text-base-content/70">Belum ada hasil scan</p>
                    <p class="text-sm text-base-content/50">Mulai scan untuk melihat hasilnya di sini</p>
                </div>
            @endif
        </x-card>
    </div>
</div>

<script>
    // Scanner JavaScript functionality will be handled by the existing scanner.js
    document.addEventListener('livewire:navigated', function () {
        // Re-initialize scanner when Livewire navigates
        if (window.QRBarcodeScanner) {
            new window.QRBarcodeScanner()
        }
    });

    // Listen for scan results from scanner.js
    document.addEventListener('qr-code-scanned', function (event) {
        @this.call('handleScannedCode', event.detail.code);
    });
</script>