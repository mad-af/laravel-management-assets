<x-info-card title="Vehicle Profile" icon="o-truck">
    @if($vehicle->vehicleProfile)
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-base-content/70">License Plate</label>
                <p class="font-mono">{{ $vehicle->vehicleProfile->license_plate ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/70">VIN</label>
                <p class="font-mono text-xs">{{ $vehicle->vehicleProfile->vin ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/70">Make</label>
                <p>{{ $vehicle->vehicleProfile->make ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/70">Model</label>
                <p>{{ $vehicle->vehicleProfile->model ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/70">Current Odometer</label>
                <p>{{ number_format($vehicle->vehicleProfile->current_odometer_km ?? 0) }} km</p>
            </div>
            <div>
                <label class="text-sm font-medium text-base-content/70">Last Service</label>
                <p>{{ $vehicle->vehicleProfile->last_service_date ? $vehicle->vehicleProfile->last_service_date->format('d M Y') : '-' }}</p>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-base-content/50">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>No vehicle profile data available</p>
            </div>
        </div>
    @endif
</x-info-card>