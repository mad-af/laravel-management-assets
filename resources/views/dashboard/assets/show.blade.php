@extends('layouts.dashboard')

@section('title', 'Detail Asset')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('assets.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Detail Asset</h1>
                    <p class="mt-1 text-base-content/70">Informasi lengkap pengguna sistem.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Asset Info Card -->
            <div class="lg:col-span-2">
                <x-assets.detail-card :asset="$asset" />
            </div>

            <!-- Quick Actions Card -->
            <div class="lg:col-span-1">
                <x-assets.quick-actions :asset="$asset" />
            </div>
        </div>
    </div>
@endsection