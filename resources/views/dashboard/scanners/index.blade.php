@extends('layouts.dashboard')

@section('title', 'QR/Barcode Scanner')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">QR/Barcode Scanner</h1>
                <p class="mt-1 text-base-content/70">Scan QR code atau barcode untuk mencari dan mengelola aset.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Scanner Interface -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Camera Scanner -->
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <h2 class="mb-4 text-lg font-semibold card-title">
                        <i data-lucide="camera" class="w-5 h-5"></i>
                        Scanner Kamera
                    </h2>

                    <!-- Camera Preview -->
                    <div class="overflow-hidden relative rounded-lg bg-base-200" style="aspect-ratio: 4/3;">
                        <video id="scanner-video" class="object-cover w-full h-full" autoplay muted playsinline></video>
                        <div id="scanner-overlay" class="flex absolute inset-0 justify-center items-center">
                            <div class="rounded-lg border-2 border-dashed border-primary"
                                style="width: 250px; height: 250px;">
                                <div class="flex justify-center items-center w-full h-full text-primary">
                                    <div class="text-center">
                                        <i data-lucide="scan-line" class="mx-auto mb-2 w-12 h-12"></i>
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
                            <i data-lucide="play" class="w-4 h-4"></i>
                            Mulai Scan
                        </button>
                        <button id="stop-scanner" class="flex-1 btn btn-outline" disabled>
                            <i data-lucide="square" class="w-4 h-4"></i>
                            Stop Scan
                        </button>
                        <button id="switch-camera" class="btn btn-ghost">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Scanner Status -->
                    <div id="scanner-status" class="mt-4">
                        <div class="alert alert-info">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            <span>Klik "Mulai Scan" untuk mengaktifkan kamera</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Results -->
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <h2 class="mb-4 text-lg font-semibold card-title">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        Hasil Scan
                    </h2>

                    <!-- Scan Result Display -->
                    <div id="scan-result" class="hidden">
                        <div class="p-4 mb-4 rounded-lg bg-base-200">
                            <div class="flex gap-2 items-center mb-2">
                                <i data-lucide="qr-code" class="w-4 h-4 text-success"></i>
                                <span class="font-semibold">Kode Terdeteksi:</span>
                            </div>
                            <div class="p-2 font-mono text-sm rounded bg-base-300" id="scanned-code"></div>
                        </div>

                        <!-- Asset Information -->
                        <div id="asset-info" class="hidden">
                            <div class="divider">Informasi Aset</div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-semibold">Nama Aset:</span>
                                    <span id="asset-name">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Kode Aset:</span>
                                    <span id="asset-code">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Kategori:</span>
                                    <span id="asset-category">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Lokasi:</span>
                                    <span id="asset-location">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Status:</span>
                                    <span id="asset-status" class="badge">-</span>
                                </div>
                            </div>

                            <div class="divider">Quick Actions</div>

                            <!-- Asset Actions -->
                            <div class="mt-4 space-y-3">
                                <!-- Quick Status Update Actions -->
                                <div class="space-y-2">
                                    <!-- Maintenance and Checkout buttons side by side -->
                                    <div id="maintenance-checkout-button-container" class="grid grid-cols-2 gap-2">
                                        <button class="justify-start w-full btn update-status-btn" data-status="maintenance" title="Set to Maintenance">
                                            <i data-lucide="settings" class="mr-2 w-4 h-4"></i>
                                            Maintenance
                                        </button>
                                        <button class="justify-start w-full btn-primary btn update-status-btn" data-status="checked_out" title="Check Out">
                                            <i data-lucide="upload" class="mr-2 w-4 h-4"></i>
                                            Check Out
                                        </button>
                                    </div>
                                    
                                    <!-- Check In button - only show when asset status is checked_out -->
                                    <div id="checkin-button-container" class="hidden">
                                        <button class="justify-center w-full btn-success btn update-status-btn" data-status="active" title="Check In (Active)">
                                            <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                                            Check In
                                        </button>
                                    </div>
                                    
                                    <!-- View Detail button -->
                                    <button class="justify-center w-full btn" id="view-asset" title="View Detail">
                                        <i data-lucide="eye" class="mr-2 w-4 h-4"></i>
                                        View Detail
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Not Found Message -->
                        <div id="asset-not-found" class="hidden">
                            <div class="alert alert-warning">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                <div>
                                    <h3 class="font-bold">Aset Tidak Ditemukan</h3>
                                    <div class="text-xs">Kode yang dipindai tidak terdaftar dalam sistem.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="scan-empty" class="py-8 text-center">
                        <i data-lucide="scan" class="mx-auto mb-4 w-16 h-16 text-base-300"></i>
                        <p class="text-base-content/70">Belum ada hasil scan</p>
                        <p class="text-sm text-base-content/50">Mulai scan untuk melihat hasilnya di sini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <h2 class="mb-4 text-lg font-semibold card-title">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    Riwayat Scan Terakhir
                </h2>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Kode</th>
                                <th>Nama Aset</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="recent-scans">
                            <tr>
                                <td colspan="5" class="py-8 text-center text-base-content/70">
                                    Belum ada riwayat scan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-scanners.scripts />
@endsection