<form wire:submit="save" class="space-y-2">
    <!-- Company Name -->
    <x-input wire:model="name" placeholder="Enter company name" required class="input-sm">
        <x-slot:label>
            <span class="text-xs font-bold label-text text-base-content">Company Name</span>
        </x-slot:label>
    </x-input>

    <!-- Company Code -->
    <x-input label="Company Code" wire:model="code" placeholder="Enter company code (max 10 chars)" maxlength="10"
        required class="input-sm" />

    <!-- Tax ID -->
    <x-input label="Tax ID" wire:model="tax_id" placeholder="Enter tax identification number" class="input-sm" />

    <!-- Location Single Select -->
    <x-select label="Location" wire:model="location_id" :options="$allLocations" option-value="id" option-label="name"
        placeholder="Select location for this company" class="select-sm" />

    <!-- Address -->
    <x-textarea label="Address" wire:model="address" placeholder="Enter company address" rows="3" class="textarea-sm" />

    <!-- Phone -->
    <x-input label="Phone" wire:model="phone" placeholder="Enter phone number" type="tel" class="input-sm" />

    <!-- Email -->
    <x-input label="Email" wire:model="email" placeholder="Enter email address" type="email" class="input-sm" />

    <!-- Website -->
    <x-input label="Website" wire:model="website" placeholder="https://example.com" type="url" class="input-sm" />

    <!-- Image Upload -->
    <x-file label="Company Logo" wire:model="image" accept="image/*" class="!file-input-sm" />

    <!-- Status Toggle -->
    <x-toggle label="Active Status" wire:model="is_active" right />

    <!-- Action Buttons -->
    <div class="flex gap-2 pt-4">
        <x-button type="submit" class="flex-1 btn-primary btn-sm" spinner="save">
            <x-icon name="o-check" class="mr-2 w-4 h-4" />
            {{ $isEdit ? 'Update Company' : 'Create Company' }}
        </x-button>

        <x-button type="button" class="flex-1 btn-outline btn-sm"
            wire:click="{{ $isEdit ? '$dispatch(\'closeEditDrawer\')' : '$dispatch(\'closeDrawer\')' }}">
            Cancel
        </x-button>
    </div>
</form>