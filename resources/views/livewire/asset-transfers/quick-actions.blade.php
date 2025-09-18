<!-- Card Quick Actions -->
<div class="shadow-sm card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-bolt" class="w-5 h-5" />
            Quick Actions
        </h2>

        <div class="mt-4 space-y-3">
            @if($quickActionsData['status'] === \App\Enums\AssetTransferStatus::PENDING->value)
                <button class="w-full btn btn-outline btn-primary btn-sm" onclick="if(confirm('Edit transfer?')) { @this.call('openEditModal') }">
                    <x-icon name="o-pencil" class="w-4 h-4" />
                    Edit Transfer
                </button>

                <button class="w-full btn btn-outline btn-success btn-sm" onclick="if(confirm('Approve this transfer?')) { @this.call('updateStatus', 'approved') }">
                    <x-icon name="o-check" class="w-4 h-4" />
                    Approve
                </button>

                <button class="w-full btn btn-outline btn-error btn-sm" onclick="if(confirm('Reject this transfer?')) { @this.call('updateStatus', 'rejected') }">
                    <x-icon name="o-x-mark" class="w-4 h-4" />
                    Reject
                </button>
            @elseif($quickActionsData['status'] === \App\Enums\AssetTransferStatus::APPROVED->value)
                <button class="btn btn-info btn-sm" onclick="if(confirm('Start this transfer?')) { @this.call('updateStatus', 'in_progress') }">
                    <x-icon name="o-truck" class="w-4 h-4" />
                    Start Transfer
                </button>
            @endif


            <button class="w-full btn btn-outline btn-sm" wire:click="openEditModal">
                <x-icon name="o-pencil" class="w-4 h-4" />
                Edit Transfer
            </button>
        </div>
    </div>
</div>