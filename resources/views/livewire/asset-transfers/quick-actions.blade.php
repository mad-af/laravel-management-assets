<!-- Card Quick Actions -->
<div class="shadow-sm card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-bolt" class="w-5 h-5" />
            Quick Actions
        </h2>

        <div class="mt-4 space-y-3">
            @php
                $currentStatus = $quickActionsData['status'] ?? null;
            @endphp

            @if($currentStatus === 'shipped')
                <button class="w-full btn btn-info btn-sm"
                    onclick="if(confirm('Tandai transfer sebagai terkirim?')) { @this.call('updateStatus', 'delivered') }">
                    <x-icon name="o-check" class="w-4 h-4" />
                    Tandai Terkirim
                </button>
            @elseif($currentStatus === 'delivered')
                <div class="alert alert-success">
                    <span>Transfer ini sudah selesai (terkirim).</span>
                </div>
            @endif

            <button class="w-full btn btn-sm" wire:click="openEditModal">
                <x-icon name="o-pencil" class="w-4 h-4" />
                Edit Transfer
            </button>
        </div>
    </div>
</div>
