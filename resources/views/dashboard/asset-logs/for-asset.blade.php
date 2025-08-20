@extends('layouts.dashboard')

@section('title', 'Riwayat Asset - ' . $asset->name)

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('assets.show', $asset) }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Riwayat Asset</h1>
                    <p class="mt-1 text-base-content/70">Semua aktivitas untuk {{ $asset->name }} ({{ $asset->code }}).</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline btn-sm">
                    <i data-lucide="eye" class="mr-2 w-4 h-4"></i>
                    Lihat Asset
                </a>
                <button id="export-logs" class="btn btn-outline btn-sm">
                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                    Export CSV
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Asset Info Summary -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ $asset->name }}</div>
                        <div class="text-sm text-base-content/70">Nama Asset</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-secondary font-mono">{{ $asset->code }}</div>
                        <div class="text-sm text-base-content/70">Kode Asset</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-accent">{{ $logs->total() }}</div>
                        <div class="text-sm text-base-content/70">Total Aktivitas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg">
                            @if($asset->status === 'active')
                                <span class="badge badge-success badge-lg">Aktif</span>
                            @elseif($asset->status === 'maintenance')
                                <span class="badge badge-warning badge-lg">Maintenance</span>
                            @elseif($asset->status === 'retired')
                                <span class="badge badge-error badge-lg">Retired</span>
                            @else
                                <span class="badge badge-ghost badge-lg">{{ ucfirst($asset->status) }}</span>
                            @endif
                        </div>
                        <div class="text-sm text-base-content/70">Status Saat Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 shadow-xl card bg-base-100">
            <div class="card-body">
                <form method="GET" action="{{ route('asset-logs.for-asset', $asset) }}">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Action</span>
                            </label>
                            <select name="action" class="select select-bordered">
                                <option value="">Semua Action</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">User</span>
                            </label>
                            <select name="user_id" class="select select-bordered">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Dari Tanggal</span>
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-bordered">
                        </div>
                        <div class="form-control">
                            <label class="block mb-1 label">
                                <span class="label-text">Sampai Tanggal</span>
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-bordered">
                        </div>
                    </div>
                    <div class="flex gap-2 justify-end pt-4">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-lucide="filter" class="mr-2 w-4 h-4"></i>
                            Filter
                        </button>
                        <a href="{{ route('asset-logs.for-asset', $asset) }}" class="btn btn-ghost btn-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Asset Logs Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold card-title">Riwayat Aktivitas</h2>
                    <div class="text-sm text-base-content/70">
                        Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} aktivitas
                    </div>
                </div>
                
                @if($logs->count() > 0)
                    <x-asset-logs.table :logs="$logs" :show-asset="false" />

                    @if($logs->hasPages())
                        <div class="mt-6">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <i data-lucide="history" class="mx-auto w-16 h-16 text-base-content/30 mb-4"></i>
                        <h3 class="text-lg font-medium text-base-content/70 mb-2">Belum Ada Aktivitas</h3>
                        <p class="text-base-content/50">Asset ini belum memiliki riwayat aktivitas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-asset-logs.scripts />
@endsection