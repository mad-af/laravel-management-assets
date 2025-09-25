<x-info-card title="Quick Actions" icon="o-bolt">
    <div class="space-y-2">
        <button wire:click="addOdometerLog" class="w-full btn  btn-sm btn-primary">
            <x-icon name="o-calculator" class="w-4 h-4" />
            Add Odometer
        </button>

        <button wire:click="editProfile" class="w-full btn  btn-sm">
            <x-icon name="o-truck" class="w-4 h-4" />
            Save Vehicle Profile
        </button>
    </div>
</x-info-card>