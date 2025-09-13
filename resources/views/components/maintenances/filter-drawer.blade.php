<div class="z-50 drawer drawer-end">
    <input id="filter-drawer" type="checkbox" class="drawer-toggle" />
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
                        <span class="font-medium label-text">Status</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option value="">All Status</option>
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Priority</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option value="">All Priorities</option>
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                        <option>Critical</option>
                    </select>
                </div>

                <!-- Maintenance Type Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Maintenance Type</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option value="">All Types</option>
                        <option>Preventive</option>
                        <option>Corrective</option>
                        <option>Emergency</option>
                        <option>Routine</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Date From</span>
                    </label>
                    <input type="date" class="w-full input input-bordered input-sm" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Date To</span>
                    </label>
                    <input type="date" class="w-full input input-bordered input-sm" />
                </div>

                <!-- Assigned Technician Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Assigned Technician</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option value="">All Technicians</option>
                        <option>John Doe</option>
                        <option>Jane Smith</option>
                        <option>Mike Johnson</option>
                    </select>
                </div>

                <!-- Asset Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium label-text">Asset</span>
                    </label>
                    <input type="text" class="w-full input input-bordered input-sm" placeholder="Search asset..." />
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