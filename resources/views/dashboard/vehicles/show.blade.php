@extends('layouts.dashboard')

@section('title', 'Vehicle Details')

@section('content')

    <livewire:dashboard-content-header 
        title='Vehicle Details' 
        description='{{ $vehicle->name }}' 
        showBackButton />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Vehicle Information -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information Card -->
            <div class="shadow card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Basic Information</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Vehicle Name</label>
                            <p class="font-medium">{{ $vehicle->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Asset Code</label>
                            <p class="font-mono">{{ $vehicle->code }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Category</label>
                            <p>{{ $vehicle->category->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Location</label>
                            <p>{{ $vehicle->location->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Status</label>
                            @php
                                $statusColors = [
                                    'active' => 'badge-success',
                                    'damaged' => 'badge-error',
                                    'lost' => 'badge-error',
                                    'maintenance' => 'badge-warning',
                                    'checked_out' => 'badge-info',
                                ];
                                $statusColor = $statusColors[$vehicle->status->value] ?? 'badge-neutral';
                            @endphp
                            <x-badge value="{{ ucfirst($vehicle->status->value) }}" class="{{ $statusColor }}" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Purchase Date</label>
                            <p>{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                    @if($vehicle->description)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-base-content/70">Description</label>
                            <p class="mt-1">{{ $vehicle->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vehicle Profile Card -->
            @if($vehicle->vehicleProfile)
            <div class="shadow card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Vehicle Profile</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-base-content/70">License Plate</label>
                            <p class="font-mono font-medium">{{ $vehicle->vehicleProfile->plate_no ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">VIN</label>
                            <p class="font-mono text-sm">{{ $vehicle->vehicleProfile->vin ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Brand</label>
                            <p>{{ $vehicle->vehicleProfile->brand ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Model</label>
                            <p>{{ $vehicle->vehicleProfile->model ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Year Purchase</label>
                            <p>{{ $vehicle->vehicleProfile->year_purchase ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Year Manufacture</label>
                            <p>{{ $vehicle->vehicleProfile->year_manufacture ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Current Odometer</label>
                            <p>{{ $vehicle->vehicleProfile->current_odometer_km ? number_format($vehicle->vehicleProfile->current_odometer_km) . ' km' : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Last Service Date</label>
                            <p>{{ $vehicle->vehicleProfile->last_service_date ? $vehicle->vehicleProfile->last_service_date->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Column - Actions & Info -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Quick Actions -->
            <div class="shadow card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Quick Actions</h2>
                    <div class="space-y-2">
                        <button class="w-full btn btn-primary btn-sm">
                            <x-icon name="o-pencil" class="w-4 h-4" />
                            Edit Vehicle
                        </button>
                        <button class="w-full btn btn-outline btn-sm">
                            <x-icon name="o-wrench-screwdriver" class="w-4 h-4" />
                            Schedule Maintenance
                        </button>
                        <button class="w-full btn btn-outline btn-sm">
                            <x-icon name="o-document-text" class="w-4 h-4" />
                            View Logs
                        </button>
                    </div>
                </div>
            </div>

            <!-- Asset Information -->
            <div class="shadow card bg-base-100">
                <div class="card-body">
                    <h2 class="card-title">Asset Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Asset Value</label>
                            <p class="font-medium">{{ $vehicle->value ? 'Rp ' . number_format($vehicle->value, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Condition</label>
                            @php
                                $conditionColors = [
                                    'excellent' => 'badge-success',
                                    'good' => 'badge-info',
                                    'fair' => 'badge-warning',
                                    'poor' => 'badge-error',
                                ];
                                $conditionColor = $conditionColors[$vehicle->condition->value] ?? 'badge-neutral';
                            @endphp
                            <x-badge value="{{ ucfirst($vehicle->condition->value) }}" class="{{ $conditionColor }}" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-base-content/70">Last Seen</label>
                            <p class="text-sm">{{ $vehicle->last_seen_at ? $vehicle->last_seen_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
@endsection