@props(['categorie', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="flex gap-2 items-center mb-6 card-title text-base-content">
            <i data-lucide="info" class="w-5 h-5"></i>
            Category Information
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-base-content/70">Category Name</label>
                <div class="flex gap-4 items-center mt-1">
                    <p class="text-base-content">{{ $categorie->name }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Status</label>
                <p class="mt-1">
                    @if($categorie->is_active)
                        <span class="badge badge-success badge-sm">Active</span>
                    @else
                        <span class="badge badge-error badge-sm">Inactive</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Created At</label>
                <p class="mt-1 text-base-content">{{ $categorie->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-base-content/70">Last Updated</label>
                <p class="mt-1 text-base-content">{{ $categorie->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>