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
                <a href="{{ route('assets.show', $asset) }}" class="btn  btn-sm">
                    <i data-lucide="eye" class="mr-2 w-4 h-4"></i>
                    Lihat Asset
                </a>
                <button id="export-logs" class="btn  btn-sm">
                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                    Export CSV
                </button>
            </div>
        </div>

        <livewire:asset-logs.for-asset :asset="$asset" />
    </div>

    <x-asset-logs.scripts />
@endsection