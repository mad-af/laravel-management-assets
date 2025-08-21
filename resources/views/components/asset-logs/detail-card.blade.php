@props(['assetLog', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="info" class="w-5 h-5"></i>
            Asset Log Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Asset</label>
                <div class="flex gap-4 items-center mt-1">
                    @if($assetLog->asset)
                        <x-avatar initials="{{ substr($assetLog->asset->name, 0, 2) }}" size="sm" placeholder="true" />
                        <div>
                            <p class="text-base-content">{{ $assetLog->asset->name }}</p>
                            <p class="text-sm text-base-content/70">{{ $assetLog->asset->code ?? 'N/A' }}</p>
                        </div>
                    @else
                        <div class="text-base-content/50">Asset has been deleted</div>
                    @endif
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Action</label>
                <p class="mt-1">
                    <span class="badge badge-primary badge-sm">{{ ucfirst(str_replace('_', ' ', $assetLog->action)) }}</span>
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">User</label>
                @if($assetLog->user)
                    <div class="flex gap-2 items-center mt-1">
                        <x-avatar initials="{{ substr($assetLog->user->name, 0, 2) }}" size="xs" placeholder="true" />
                        <div>
                            <p class="text-base-content">{{ $assetLog->user->name }}</p>
                            <p class="text-sm text-base-content/70">{{ $assetLog->user->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-1 text-base-content/50">System</p>
                @endif
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Date</label>
                <p class="mt-1 text-base-content">{{ $assetLog->created_at->format('M d, Y H:i') }}</p>
            </div>

            @if($assetLog->changed_fields)
            <div class="md:col-span-2">
                <label class="text-sm font-semibold text-base-content/70">Changed Fields</label>
                <div class="mt-1 space-y-2">
                    @foreach($assetLog->changed_fields as $field => $change)
                        <div class="flex justify-between items-center p-2 bg-base-200 rounded">
                            <span class="font-medium text-base-content">{{ ucfirst($field) }}:</span>
                            <div class="text-sm">
                                <span class="text-red-500">{{ $change['old'] ?? 'N/A' }}</span>
                                <i data-lucide="arrow-right" class="w-4 h-4 mx-2 inline"></i>
                                <span class="text-green-500">{{ $change['new'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($assetLog->notes)
            <div class="md:col-span-2">
                <label class="text-sm font-semibold text-base-content/70">Notes</label>
                <p class="mt-1 text-base-content">{{ $assetLog->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>