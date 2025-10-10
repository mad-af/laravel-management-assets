@extends('layouts.dashboard')

@section('title', 'Vehicle Details')

@section('content')

    <livewire:dashboard-content-header title='Vehicle Details' description='{{ $vehicle->name }}' showBackButton />

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
        <!-- Left Column - Vehicle Information -->
        <div class="space-y-4 lg:col-span-4">
            <!-- Basic Information Card -->
            <livewire:vehicles.basic-info :vehicle="$vehicle" />

            <!-- Vehicle Profile Card -->
            <livewire:vehicles.vehicle-profile-info :vehicle="$vehicle" />

            <!-- History Card -->
            <livewire:vehicles.vehicle-history :vehicle="$vehicle" />

        </div>

        <!-- Right Column - Actions & Info -->
        <div class="space-y-4 lg:col-span-2">
            <!-- Quick Actions Card -->
            <livewire:vehicles.quick-actions-card :vehicle="$vehicle" />
        </div>

    </div>

    <!-- Drawer Component -->
    <livewire:vehicles.drawer />

@endsection