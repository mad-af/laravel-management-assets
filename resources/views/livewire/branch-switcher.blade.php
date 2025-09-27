<div>

    <x-select-group :options="$grouped" wire:model.live="selectedUser"
        class="select-sm !w-48 border border-base-300 focus:outline-none focus:border-primary">
        <x-slot:prepend>
            <span
                class="flex items-center px-3 text-xs font-semibold rounded-l-md border join-item bg-base-200 border-base-300">
                <x-icon name="o-building-office-2" class="w-4 h-4" />
            </span>
        </x-slot:prepend>
    </x-select-group>

</div>