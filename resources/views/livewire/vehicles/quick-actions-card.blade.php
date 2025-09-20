<x-info-card title="Quick Actions" icon="o-bolt">
    <div class="space-y-2">
        <button wire:click="addOdometerLog" class="w-full btn btn-outline btn-sm btn-primary">
            <x-icon name="o-calculator" class="w-4 h-4" />
            Add Odometer
        </button>

        <button wire:click="editProfile" class="w-full btn btn-outline btn-sm">
            <x-icon name="o-pencil-square" class="w-4 h-4" />
            Edit Profile
        </button>
    </div>
</x-info-card>