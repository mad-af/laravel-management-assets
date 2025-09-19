<form wire:submit="save" class="space-y-2">
    <!-- Vehicle Name -->
    <x-input name="name" label="Vehicle Name" class="input-sm" wire:model="name" placeholder="Enter vehicle name" required />

    <!-- License Plate -->
    <x-input name="license_plate" label="License Plate" class="input-sm" wire:model="license_plate" placeholder="Enter license plate" required />

    <!-- Brand -->
    <x-input name="brand" label="Brand" class="input-sm" wire:model="brand" placeholder="Enter vehicle brand" />

    <!-- Model -->
    <x-input name="model" label="Model" class="input-sm" wire:model="model" placeholder="Enter vehicle model" />

    <!-- Year -->
    <x-input name="year" label="Year" class="input-sm" wire:model="year" type="number" placeholder="Enter manufacturing year" />

    <!-- Color -->
    <x-input name="color" label="Color" class="input-sm" wire:model="color" placeholder="Enter vehicle color" />

    <!-- Engine Number -->
    <x-input name="engine_number" label="Engine Number" class="input-sm" wire:model="engine_number" placeholder="Enter engine number" />

    <!-- Chassis Number -->
    <x-input name="chassis_number" label="Chassis Number" class="input-sm" wire:model="chassis_number" placeholder="Enter chassis number" />

    <!-- Fuel Type -->
    <x-select name="fuel_type" label="Fuel Type" class="select-sm" wire:model="fuel_type" :options="[['value' => 'gasoline', 'label' => 'Gasoline'], ['value' => 'diesel', 'label' => 'Diesel'], ['value' => 'electric', 'label' => 'Electric'], ['value' => 'hybrid', 'label' => 'Hybrid']]"
        option-value="value" option-label="label" placeholder="Select fuel type" />

    <!-- Status -->
    <x-select name="status" label="Status" class="select-sm" wire:model="status" :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive'], ['value' => 'maintenance', 'label' => 'Maintenance']]"
        option-value="value" option-label="label" placeholder="Select status" required />

    <!-- Purchase Date -->
    <x-datetime name="purchase_date" label="Purchase Date" class="input-sm" wire:model="purchase_date" type="date" />

    <!-- Purchase Price -->
    <x-input name="purchase_price" label="Purchase Price" class="input-sm" wire:model="purchase_price" type="number" step="0.01" placeholder="Enter purchase price" />

    <!-- Current Odometer -->
    <x-input name="current_odometer" label="Current Odometer (km)" class="input-sm" wire:model="current_odometer" type="number" placeholder="Enter current odometer reading" />

    <!-- Notes -->
    <x-textarea name="notes" class="textarea-sm" label="Notes" wire:model="notes" rows="3" placeholder="Enter additional notes" />



    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Cancel" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('closeDrawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            {{ $vehicleId ? 'Update' : 'Save' }}
        </button>
    </div>
</form>
