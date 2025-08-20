<div class="mb-6 shadow-xl card bg-base-100">
    <div class="card-body">
        <form method="GET" action="{{ route('assets.index') }}">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Search</span>
                    </label>
                    <input type="text" name="Search" value="{{ request('search') }}" placeholder="Search..."
                        class="input input-bordered" />
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Category</span>
                    </label>
                    <select name="category" class="select select-bordered">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Location</span>
                    </label>
                    <select name="location" class="select select-bordered">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="block mb-1 label">
                        <span class="label-text">Status</span>
                    </label>
                    <select name="status" class="select select-bordered">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance
                        </option>
                        <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 justify-end pt-4">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="filter" class="mr-2 w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('assets.index') }}" class="btn btn-ghost">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>