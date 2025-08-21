@props(['location', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="info" class="w-5 h-5"></i>
            Location Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Location Name</label>
                <div class="flex gap-4 items-center mt-1">
                    <p class="text-base-content">{{ $location->name }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Status</label>
                <p class="mt-1">
                    @if($location->is_active)
                        <span class="badge badge-success badge-sm">Active</span>
                    @else
                        <span class="badge badge-error badge-sm">Inactive</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Created At</label>
                <p class="mt-1 text-base-content">{{ $location->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Last Updated</label>
                <p class="mt-1 text-base-content">{{ $location->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>