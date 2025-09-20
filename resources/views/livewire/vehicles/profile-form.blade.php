<form action="{{ route('vehicles.store-profile') }}" method="POST" class="space-y-2">
    @csrf

    <!-- Asset Selection -->
    <x-select name="asset_id" label="Asset" class="select-sm" wire:model.live="asset_id" :options="$assets"
        option-value="id" option-label="display_name" placeholder="Select asset" required />

    <!-- License Plate -->
    <x-input name="plate_no" label="License Plate" class="input-sm" wire:model="plate_no"
        placeholder="Enter license plate" required />

    <!-- Brand -->
    <x-input name="brand" label="Brand" class="input-sm" wire:model="brand" placeholder="Enter vehicle brand"
        required />

    <!-- Model -->
    <x-input name="model" label="Model" class="input-sm" wire:model="model" placeholder="Enter vehicle model"
        required />

    <!-- VIN -->
    <x-input name="vin" label="VIN" class="input-sm" wire:model="vin" placeholder="Enter VIN number" />

    <!-- Purchase Year -->
    <x-input name="year_purchase" label="Purchase Year" class="input-sm" wire:model="year_purchase" type="number"
        min="1900" max="{{ date('Y') + 1 }}" placeholder="Enter purchase year" />

    <!-- Manufacture Year -->
    <x-input name="year_manufacture" label="Manufacture Year" class="input-sm" wire:model="year_manufacture"
        type="number" min="1900" max="{{ date('Y') + 1 }}" placeholder="Enter manufacture year" />

    <!-- Current Odometer -->
    <x-input name="current_odometer_km" label="Current Odometer (km)" class="input-sm" wire:model="current_odometer_km"
        type="number" min="0" placeholder="Enter current odometer reading" :readonly=$isEdit />

    <!-- Last Service Date -->
    <x-datetime name="last_service_date" label="Last Service Date" class="input-sm" wire:model="last_service_date"
        type="date" />

    <!-- Service Interval (KM) -->
    <x-input name="service_interval_km" label="Service Interval (km)" class="input-sm" wire:model="service_interval_km"
        type="number" min="1" placeholder="Enter service interval in km" />

    <!-- Service Interval (Days) -->
    <x-input name="service_interval_days" label="Service Interval (days)" class="input-sm"
        wire:model="service_interval_days" type="number" min="1" placeholder="Enter service interval in days" />

    <!-- Service Target Odometer -->
    <x-input name="service_target_odometer_km" label="Service Target Odometer (km)" class="input-sm"
        wire:model="service_target_odometer_km" type="number" min="0"
        placeholder="Enter target odometer for next service" />

    <!-- Next Service Date -->
    <x-datetime name="next_service_date" label="Next Service Date" class="input-sm" wire:model="next_service_date"
        type="date" />

    <!-- Annual Tax Due Date -->
    <x-datetime name="annual_tax_due_date" label="Annual Tax Due Date" class="input-sm" wire:model="annual_tax_due_date"
        type="date" />



    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Cancel" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('closeDrawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            {{ $vehicleId ? 'Update' : 'Save' }}
        </button>
    </div>
</form>