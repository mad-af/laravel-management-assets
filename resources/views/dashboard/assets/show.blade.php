@extends('layouts.dashboard')

@section('title', 'Asset Details')

@section('content')

    <livewire:dashboard-content-header title='Asset Details' description='{{ $asset->name }}' showBackButton />

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
        <!-- Left Column - Asset Information -->
        <div class="space-y-4 lg:col-span-4">
            <!-- Basic Information Card -->
            <livewire:assets.basic-info :asset="$asset" />

            <!-- History Monitor Card -->
            <livewire:assets.history-monitor :asset="$asset" />

            <!-- Activity Log Card -->
            <livewire:assets.activity-log :asset="$asset" />

        </div>

        <!-- Right Column - Actions & Stats -->
        <div class="space-y-4 lg:col-span-2">
            <!-- Quick Actions Card -->
            <livewire:assets.quick-actions-card :asset="$asset" />

            <!-- Asset Stats Card -->
            <livewire:assets.stats-card :asset="$asset" />
        </div>

    </div>

    <!-- Drawer Component -->
    <livewire:assets.drawer />

@endsection