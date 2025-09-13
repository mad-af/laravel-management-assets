<div class="z-50 drawer drawer-end">
    <input id="filter-drawer" type="checkbox" class="drawer-toggle" onchange="cleanUrlParams()" />
    <div class="drawer-content">
        <!-- Page content here -->
        {{ $slot }}
    </div>
    <div class="drawer-side">
        <label for="filter-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="p-4 w-80 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Filter Maintenances</h2>
                <label for="filter-drawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-mary-icon name="o-x-mark" class="w-5 h-5" />
                </label>
            </div>

            <!-- Filter Form -->
            <form class="space-y-3">
                <!-- Status Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Status</span>
                    </label>
                    <select name="status" class="w-full select select-bordered select-sm">
                        <option value="">All Status</option>
                        @foreach(\App\Enums\MaintenanceStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Priority</span>
                    </label>
                    <select name="priority" class="w-full select select-bordered select-sm">
                        <option value="">All Priorities</option>
                        @foreach(\App\Enums\MaintenancePriority::cases() as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Maintenance Type Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Maintenance Type</span>
                    </label>
                    <select name="type" class="w-full select select-bordered select-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Enums\MaintenanceType::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Date From</span>
                    </label>
                    <input type="date" name="date_from" class="w-full input input-bordered input-sm" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Date To</span>
                    </label>
                    <input type="date" name="date_to" class="w-full input input-bordered input-sm" />
                </div>

                <!-- Assigned Technician Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Assigned Technician</span>
                    </label>
                    <select name="assigned_to" class="w-full select select-bordered select-sm">
                        <option value="">All Technicians</option>
                        @php
                            $users = \App\Models\User::orderBy('name')->get();
                        @endphp
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Asset Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Asset</span>
                    </label>
                    <select name="asset_id" class="w-full select select-bordered select-sm">
                        <option value="">All Assets</option>
                        @php
                            $assets = \App\Models\Asset::with('category')->orderBy('name')->get();
                        @endphp
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-3">
                    <button type="submit" class="flex-1 btn btn-primary btn-sm">
                        <x-mary-icon name="o-funnel" class="mr-2 w-4 h-4" />
                        Apply Filters
                    </button>
                    <button type="button" class="flex-1 btn btn-outline btn-sm" onclick="clearFilters()">
                        Clear All
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>