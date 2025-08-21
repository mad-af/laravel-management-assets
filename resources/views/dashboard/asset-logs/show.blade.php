@extends('layouts.dashboard')

@section('title', 'Detail Asset Log')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('asset-logs.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Detail Asset Log</h1>
                    <p class="mt-1 text-base-content/70">Informasi lengkap aktivitas asset.</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('asset-logs.for-asset', $log->asset_id) }}" class="btn btn-outline btn-sm">
                    <i data-lucide="history" class="mr-2 w-4 h-4"></i>
                    Riwayat Asset
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Log Detail Card -->
            <div class="lg:col-span-2">
                <x-asset-logs.detail-card :assetLog="$log" />
            </div>

            <!-- Asset Info Card -->
            <div class="lg:col-span-1">
                @if($log->asset)
                    <div class="shadow-xl card bg-base-100">
                        <div class="card-body">
                            <h2 class="mb-4 text-lg font-semibold card-title">
                                <i data-lucide="package" class="mr-2 w-5 h-5"></i>
                                Informasi Asset
                            </h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-base-content/70">Nama Asset</label>
                                    <p class="text-base-content">{{ $log->asset->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-base-content/70">Kode Asset</label>
                                    <p class="font-mono text-base-content">{{ $log->asset->code }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-base-content/70">Kategori</label>
                                    <p class="text-base-content">{{ $log->asset->category->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-base-content/70">Lokasi</label>
                                    <p class="text-base-content">{{ $log->asset->location->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-base-content/70">Status</label>
                                    <div class="mt-1">
                                        @if($log->asset->status === 'active')
                                            <span class="badge badge-success">Aktif</span>
                                        @elseif($log->asset->status === 'maintenance')
                                            <span class="badge badge-warning">Maintenance</span>
                                        @elseif($log->asset->status === 'retired')
                                            <span class="badge badge-error">Retired</span>
                                        @else
                                            <span class="badge badge-ghost">{{ ucfirst($log->asset->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('assets.show', $log->asset) }}" class="w-full btn btn-primary btn-sm">
                                    <i data-lucide="external-link" class="mr-2 w-4 h-4"></i>
                                    Lihat Detail Asset
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="shadow-xl card bg-base-100">
                        <div class="card-body">
                            <div class="text-center text-base-content/70">
                                <i data-lucide="alert-triangle" class="mx-auto mb-4 w-12 h-12"></i>
                                <p>Asset tidak ditemukan atau telah dihapus.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection