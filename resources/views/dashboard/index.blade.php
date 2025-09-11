@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Asset Management Dashboard</h1>
                <p class="mt-1 text-base-content/70">Selamat datang! Berikut adalah ringkasan sistem manajemen aset Anda.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                    Tambah Asset
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="shadow stats bg-base-100">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i data-lucide="package" class="w-8 h-8"></i>
                    </div>
                    <div class="stat-title">Total Assets</div>
                    <div class="stat-value text-primary">{{ \App\Models\Asset::count() }}</div>
                    <div class="stat-desc">Semua aset dalam sistem</div>
                </div>
            </div>

            <div class="shadow stats bg-base-100">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i data-lucide="check-circle" class="w-8 h-8"></i>
                    </div>
                    <div class="stat-title">Active Assets</div>
                    <div class="stat-value text-success">{{ \App\Models\Asset::where('status', \App\Enums\AssetStatus::ACTIVE)->count() }}</div>
                    <div class="stat-desc">Aset yang sedang aktif</div>
                </div>
            </div>

            <div class="shadow stats bg-base-100">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i data-lucide="folder" class="w-8 h-8"></i>
                    </div>
                    <div class="stat-title">Categories</div>
                    <div class="stat-value text-info">{{ \App\Models\Category::where('is_active', true)->count() }}</div>
                    <div class="stat-desc">Kategori aktif</div>
                </div>
            </div>

            <div class="shadow stats bg-base-100">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                    </div>
                    <div class="stat-title">Locations</div>
                    <div class="stat-value text-warning">{{ \App\Models\Location::where('is_active', true)->count() }}</div>
                    <div class="stat-desc">Lokasi aktif</div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <x-card title="Aktivitas Terbaru" shadow>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <a href="{{ route('asset-logs.index') }}" class="btn btn-ghost btn-sm">
                                Lihat Semua
                                <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            @php
                                $recentLogs = \App\Models\AssetLog::with(['asset', 'user'])
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @if($recentLogs->count() > 0)
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Asset</th>
                                            <th>Aksi</th>
                                            <th>User</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentLogs as $log)
                                            <tr>
                                                <td>
                                                    @if($log->asset)
                                                        <div class="flex gap-3 items-center">
                                                            {{-- <x-avatar initials="{{ substr($log->asset->name, 0, 2) }}" size="sm"
                                                                placeholder="true" /> --}}
                                                            <div>
                                                                <div class="font-bold">{{ $log->asset->name }}</div>
                                                                <div class="text-sm opacity-50">{{ $log->asset->code }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-base-content/50">Asset Deleted</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                        $actionColors = [
                                            \App\Enums\AssetLogAction::CREATED->value => 'badge-success',
                                            \App\Enums\AssetLogAction::UPDATED->value => 'badge-info',
                                            \App\Enums\AssetLogAction::DELETED->value => 'badge-error',
                                            \App\Enums\AssetLogAction::STATUS_CHANGED->value => 'badge-warning'
                                        ];
                                        $colorClass = $actionColors[$log->action] ?? 'badge-neutral';
                                    @endphp
                                                    <span class="badge {{ $colorClass }}">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($log->user)
                                                        <div class="flex gap-2 items-center">
                                                            <x-avatar initials="{{ substr($log->user->name, 0, 2) }}" size="xs"
                                                                placeholder="true" />
                                                            <span class="text-sm">{{ $log->user->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-base-content/50">System</span>
                                                    @endif
                                                </td>
                                                <td class="text-sm">{{ $log->created_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="py-8 text-center text-base-content/50">
                                    <i data-lucide="activity" class="mx-auto mb-2 w-12 h-12"></i>
                                    <p>Belum ada aktivitas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Quick Info -->
            <div class="space-y-6">
                <x-card title="Status Asset" shadow>
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $activeCount = \App\Models\Asset::where('status', \App\Enums\AssetStatus::ACTIVE)->count();
                $inactiveCount = \App\Models\Asset::where('status', 'inactive')->count();
                $maintenanceCount = \App\Models\Asset::where('status', \App\Enums\AssetStatus::MAINTENANCE)->count();
                $disposedCount = \App\Models\Asset::where('status', 'disposed')->count();
                            @endphp
                            <div class="flex justify-between items-center">
                                <span>Active</span>
                                <div class="badge badge-success">{{ $activeCount }}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Inactive</span>
                                <div class="badge badge-warning">{{ $inactiveCount }}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Maintenance</span>
                                <div class="badge badge-info">{{ $maintenanceCount }}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Disposed</span>
                                <div class="badge badge-error">{{ $disposedCount }}</div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Asset Terbaru" shadow>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <a href="{{ route('assets.index') }}" class="btn btn-ghost btn-sm">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="space-y-3">
                            @php
                                $recentAssets = \App\Models\Asset::with(['category', 'location'])
                                    ->orderBy('created_at', 'desc')
                                    ->limit(3)
                                    ->get();
                            @endphp

                            @if($recentAssets->count() > 0)
                                @foreach($recentAssets as $asset)
                                    <div class="flex gap-3 items-center">
                                        <x-avatar initials="{{ substr($asset->name, 0, 2) }}" size="sm" placeholder="true" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium">{{ $asset->name }}</p>
                                            <p class="text-xs opacity-70">{{ $asset->code }} •
                                                {{ $asset->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div
                                            class="badge badge-{{ $asset->status === \App\Enums\AssetStatus::ACTIVE ? 'success' : ($asset->status === \App\Enums\AssetStatus::MAINTENANCE ? 'warning' : 'error') }} badge-sm">
                                            {{ ucfirst($asset->status) }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="py-4 text-center text-base-content/50">
                                    <i data-lucide="package" class="mx-auto mb-2 w-8 h-8"></i>
                                    <p class="text-sm">Belum ada asset</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
@endsection