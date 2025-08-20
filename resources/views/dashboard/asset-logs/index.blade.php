@extends('layouts.dashboard')

@section('title', 'Asset Logs')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Asset Logs</h1>
                <p class="mt-1 text-base-content/70">Monitor semua aktivitas dan perubahan asset.</p>
            </div>
            <div class="flex gap-2">
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

        <!-- Filters -->
        <x-asset-logs.filter-form :assets="$assets" :actions="$actions" :users="$users" />

        <!-- Asset Logs Table -->
        <div class="shadow-xl card bg-base-100">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold card-title">Activity Logs</h2>
                    <div class="text-sm text-base-content/70">
                        Total: {{ $logs->total() }} logs
                    </div>
                </div>
                
                <x-asset-logs.table :logs="$logs" />

                @if($logs->hasPages())
                    <div class="mt-6">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-asset-logs.scripts />
@endsection