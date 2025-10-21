<div class="shadow-sm card card-compact bg-base-100">
    <div class="card-body">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-base-content/70">Transfer Number</label>
                <p class="text-sm font-semibold">{{ $transferData['transfer_no'] ?? '-' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-base-content/70">Status</label>
                @php
                    $statusEnum = is_object($transferData['status'] ?? null)
                        ? $transferData['status']
                        : (is_string($transferData['status'] ?? null) ? \App\Enums\AssetTransferStatus::from($transferData['status']) : null);
                @endphp
                <div class="mt-1">
                    <span class="badge badge-{{ $statusEnum?->color() }} badge-sm">
                        {{ $statusEnum?->label() ?? '-' }}
                    </span>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-base-content/70">Type</label>
                <p class="text-sm">
                    {{ is_object($transferData['type'] ?? null) ? $transferData['type']->label() : ucfirst($transferData['type'] ?? '-') }}
                </p>
            </div>

            <div>
                <label class="text-sm font-medium text-base-content/70">From Branch</label>
                <p class="text-sm font-semibold">{{ $transferData['from_branch'] ?? '-' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-base-content/70">To Branch</label>
                <p class="text-sm font-semibold">{{ $transferData['to_branch'] ?? '-' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-base-content/70">Requested By</label>
                <p class="text-sm">{{ $transferData['requested_by'] ?? '-' }}</p>
            </div>

            @if(!empty($transferData['company']))
                <div>
                    <label class="text-sm font-medium text-base-content/70">Company</label>
                    <p class="text-sm">{{ $transferData['company'] }}</p>
                </div>
            @endif

            @if(!empty($transferData['requested_at']))
                <div>
                    <label class="text-sm font-medium text-base-content/70">Requested Date</label>
                    <p class="text-sm font-semibold">
                        {{ is_object($transferData['requested_at']) ? $transferData['requested_at']->format('M d, Y H:i') : $transferData['requested_at'] }}
                    </p>
                </div>
            @endif
        </div>

        @if(!empty($transferData['description']))
            <div class="mt-4">
                <label class="text-sm font-medium text-base-content/70">Description</label>
                <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $transferData['description'] }}</p>
            </div>
        @endif

        @if(!empty($transferData['reason']))
            <div class="mt-4">
                <label class="text-sm font-medium text-base-content/70">Reason</label>
                <p class="p-3 mt-1 text-sm rounded-lg bg-base-200 text-base-content">{{ $transferData['reason'] }}</p>
            </div>
        @endif

        @if(!empty($transferData['notes']))
            <div class="mt-4">
                <label class="text-sm font-medium text-base-content/70">Notes</label>
                <p class="p-3 mt-1 text-sm whitespace-pre-line rounded-lg bg-base-200 text-base-content">
                    {{ $transferData['notes'] }}</p>
            </div>
        @endif

        @php $assets = $transferData['assets'] ?? []; @endphp
        <div class="mt-6">
            <h3 class="mb-2 text-sm font-semibold text-base-content/70">Assets in Transfer</h3>
            @if(count($assets) > 0)
                <div class="divide-y divide-base-200">
                    @foreach($assets as $asset)
                        @php
                            $cond = $asset['condition'] ?? null;
                            $condLabel = is_object($cond) ? $cond->label() : (is_string($cond) ? ucfirst($cond) : '-');
                            $condColor = is_object($cond) ? $cond->color() : 'neutral';
                        @endphp
                        <div class="flex justify-between items-center py-2">
                            <div class="flex gap-3 items-center">
                                <x-icon name="o-cube" class="w-5 h-5 text-base-content/70" />
                                <div>
                                    <div class="text-sm font-medium">{{ $asset['name'] ?? '-' }}</div>
                                    @if(!empty($asset['tag_code']))
                                        <div class="text-xs text-base-content/70">Tag: {{ $asset['tag_code'] }}</div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge badge-{{ $condColor }} badge-sm">{{ $condLabel }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-base-content/70">Tidak ada item.</div>
            @endif
        </div>
    </div>
</div>