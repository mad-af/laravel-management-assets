<form wire:submit="save" class="space-y-2">
    <!-- Asset Selection -->
    <x-select name="asset_id" label="Vehicle" class="select-sm" wire:model.live="asset_id" :options="$assets"
        option-value="id" option-label="display_name" placeholder="Select vehicle" required />

    <!-- Odometer Reading -->
    <x-input name="reading_km" label="Odometer Reading (km)" class="input-sm" wire:model="reading_km" type="number"
        min="0" placeholder="Enter odometer reading" required />

    <!-- Read Date/Time -->
    <x-datetime name="read_at" label="Reading Date & Time" class="input-sm" wire:model="read_at" type="datetime-local"
        required />

    <!-- Source -->
    <x-select name="source" label="Reading Source" class="select-sm" wire:model="source" :options="$sources"
        option-value="value" option-label="label" placeholder="Select reading source" required />

    <!-- Notes -->
    <x-textarea name="notes" class="textarea-sm" label="Notes" wire:model="notes" rows="3"
        placeholder="Enter additional notes (optional)" />

    <div class="flex gap-3 justify-end pt-4">
        <x-button label="Cancel" class="btn-ghost btn-sm" type="button" wire:click="$dispatch('closeDrawer')" />
        <button class="btn btn-sm btn-primary" type="submit">
            {{ $odometerLogId ? 'Update' : 'Save' }}
        </button>
    </div>
</form>