@props([
    'assetLog',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-6 text-lg font-semibold card-title">Detail Asset Log</h2>
        
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Asset Information</span>
                </label>
                <div class="p-4 rounded-lg bg-base-200">
                    @if($assetLog->asset)
                        <div class="space-y-2">
                            <div class="text-lg font-bold">{{ $assetLog->asset->name }}</div>
                            <div class="text-sm opacity-70">Code: {{ $assetLog->asset->code }}</div>
                            <div class="text-sm opacity-70">Category: {{ $assetLog->asset->category->name ?? 'N/A' }}</div>
                        </div>
                    @else
                        <div class="text-gray-400">Asset has been deleted</div>
                    @endif
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Action</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    <span class="badge {{ $assetLog->actionBadgeColor }}">
                        {{ ucfirst(str_replace('_', ' ', $assetLog->action)) }}
                    </span>
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">User</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    @if($assetLog->user)
                        <div class="flex items-center gap-2">
                            <x-avatar initials="{{ substr($assetLog->user->name, 0, 2) }}" size="sm" placeholder="true" />
                            <div>
                                <div class="font-medium">{{ $assetLog->user->name }}</div>
                                <div class="text-sm opacity-70">{{ $assetLog->user->email }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-gray-400">System</span>
                    @endif
                </div>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Date & Time</span>
                </label>
                <div class="p-3 rounded-lg bg-base-200">
                    <div class="font-medium">{{ $assetLog->created_at->format('d M Y') }}</div>
                    <div class="text-sm opacity-70">{{ $assetLog->created_at->format('H:i:s') }}</div>
                </div>
            </div>
            
            @if($assetLog->changed_fields)
            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="font-semibold label-text">Changed Fields</span>
                </label>
                <div class="p-4 rounded-lg bg-base-200">
                    <div class="space-y-2">
                        @foreach($assetLog->changed_fields as $field => $change)
                            <div class="flex justify-between items-center p-2 bg-base-100 rounded">
                                <span class="font-medium">{{ ucfirst($field) }}:</span>
                                <div class="text-sm">
                                    <span class="text-red-500">{{ $change['old'] ?? 'N/A' }}</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 mx-2 inline"></i>
                                    <span class="text-green-500">{{ $change['new'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            @if($assetLog->notes)
            <div class="form-control md:col-span-2">
                <label class="label">
                    <span class="font-semibold label-text">Notes</span>
                </label>
                <div class="p-4 rounded-lg bg-base-200">
                    {{ $assetLog->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>