@extends('layouts.dashboard')

@section('title', 'Asset Details')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('assets.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Asset Details</h1>
                    <p class="mt-1 text-base-content/70">Informasi lengkap asset.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Asset Information -->
            <div class="lg:col-span-2">
                <x-assets.detail-card :asset="$asset" />
            </div>

            <!-- Quick Actions & Stats -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <x-assets.quick-actions :asset="$asset" />

                <!-- Asset Stats -->
                <x-assets.stats-card :asset="$asset" />
            </div>
        </div>

        <!-- Activity Log -->
        <div class="mt-8">
            <x-assets.activity-log :asset="$asset" />
        </div>
    </div>
@endsection