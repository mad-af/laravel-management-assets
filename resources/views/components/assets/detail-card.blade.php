@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="info" class="w-5 h-5"></i>
            Asset Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Asset Code</label>
                <p class="mt-1 font-mono text-base-content">{{ $asset->code }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Asset Name</label>
                <p class="mt-1 text-base-content">{{ $asset->name }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Category</label>
                <p class="mt-1">
                    <span class="badge badge-outline badge-sm">{{ $asset->category->name }}</span>
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Location</label>
                <p class="mt-1">
                    <span class="badge badge-outline badge-sm">{{ $asset->location->name }}</span>
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Status</label>
                <p class="mt-1">
                    @if($asset->status === 'active')
                        <span class="badge badge-success badge-sm">Active</span>
                    @elseif($asset->status === 'inactive')
                        <span class="badge badge-warning badge-sm">Inactive</span>
                    @elseif($asset->status === 'maintenance')
                        <span class="badge badge-info badge-sm">Maintenance</span>
                    @else
                        <span class="badge badge-error badge-sm">Disposed</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Condition</label>
                <p class="mt-1">
                    @if($asset->condition === 'excellent')
                        <span class="badge badge-success badge-sm">Excellent</span>
                    @elseif($asset->condition === 'good')
                        <span class="badge badge-primary badge-sm">Good</span>
                    @elseif($asset->condition === 'fair')
                        <span class="badge badge-warning badge-sm">Fair</span>
                    @else
                        <span class="badge badge-error badge-sm">Poor</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Asset Value</label>
                <p class="mt-1 font-semibold text-success">{{ $asset->formatted_value }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Purchase Date</label>
                <p class="mt-1 text-base-content">
                    {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Created At</label>
                <p class="mt-1 text-base-content">{{ $asset->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Last Updated</label>
                <p class="mt-1 text-base-content">{{ $asset->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        @if($asset->description)
            <div class="mt-6">
                <label class="text-sm font-semibold text-base-content/70">Description</label>
                <p class="p-4 mt-1 rounded-lg bg-base-200 text-base-content">{{ $asset->description }}</p>
            </div>
        @endif
    </div>
</div>