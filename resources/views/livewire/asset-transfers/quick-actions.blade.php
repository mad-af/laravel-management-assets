<!-- Card Quick Actions -->
<div class="shadow-xl card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-bolt" class="w-5 h-5" />
            Quick Actions
        </h2>

        <div class="mt-4 space-y-3">
            @if($quickActionsData['status'] === 'pending')
                <button class="btn btn-primary btn-sm" wire:click="openEditModal">
                    <x-icon name="o-pencil" class="w-4 h-4" />
                    Edit Transfer
                </button>

                <button class="btn btn-success btn-sm" wire:click="openStatusModal">
                    <x-icon name="o-check" class="w-4 h-4" />
                    Approve
                </button>

                <button class="btn btn-error btn-sm" wire:click="openStatusModal">
                    <x-icon name="o-x-mark" class="w-4 h-4" />
                    Reject
                </button>
            @elseif($quickActionsData['status'] === 'approved')
                <button class="btn btn-info btn-sm" wire:click="openStatusModal">
                    <x-icon name="o-truck" class="w-4 h-4" />
                    Start Transfer
                </button>
            @elseif($quickActionsData['status'] === 'in_transit')
                <button class="btn btn-success btn-sm" wire:click="openStatusModal">
                    <x-icon name="o-check-circle" class="w-4 h-4" />
                    Complete Transfer
                </button>
            @endif


            <button class="w-full btn btn-outline btn-sm" wire:click="openEditModal">
                <x-icon name="o-pencil" class="w-4 h-4" />
                Edit Transfer
            </button>
        </div>
    </div>
</div>