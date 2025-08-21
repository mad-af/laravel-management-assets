@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-4 text-xl card-title">Asset Information</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-gray-600">Asset Code</label>
                <p class="px-3 py-2 mt-1 font-mono text-lg bg-gray-100 rounded">{{ $asset->code }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Asset Name</label>
                <p class="mt-1 text-lg">{{ $asset->name }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Category</label>
                <p class="mt-1 text-lg">
                    <span class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->category->name }}</span>
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Location</label>
                <p class="mt-1 text-lg">
                    <span class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->location->name }}</span>
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Status</label>
                <p class="mt-1 text-lg">
                    @if($asset->status === 'active')
                        <span class="text-xs whitespace-nowrap badge badge-success">Active</span>
                    @elseif($asset->status === 'inactive')
                        <span class="text-xs whitespace-nowrap badge badge-warning">Inactive</span>
                    @elseif($asset->status === 'maintenance')
                        <span class="text-xs whitespace-nowrap badge badge-info">Maintenance</span>
                    @else
                        <span class="text-xs whitespace-nowrap badge badge-error">Disposed</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Condition</label>
                <p class="mt-1 text-lg">
                    @if($asset->condition === 'excellent')
                        <span class="text-xs whitespace-nowrap badge badge-success">Excellent</span>
                    @elseif($asset->condition === 'good')
                        <span class="text-xs whitespace-nowrap badge badge-primary">Good</span>
                    @elseif($asset->condition === 'fair')
                        <span class="text-xs whitespace-nowrap badge badge-warning">Fair</span>
                    @else
                        <span class="text-xs whitespace-nowrap badge badge-error">Poor</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Asset Value</label>
                <p class="mt-1 text-lg font-semibold text-green-600">${{ number_format($asset->value, 2) }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Purchase Date</label>
                <p class="mt-1 text-lg">
                    {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Created At</label>
                <p class="mt-1 text-lg">{{ $asset->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-600">Last Updated</label>
                <p class="mt-1 text-lg">{{ $asset->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        @if($asset->description)
            <div class="mt-6">
                <label class="text-sm font-semibold text-gray-600">Description</label>
                <p class="p-4 mt-1 text-lg bg-gray-50 rounded-lg">{{ $asset->description }}</p>
            </div>
        @endif
    </div>
</div>