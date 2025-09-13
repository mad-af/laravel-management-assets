<div class="z-50 drawer drawer-end">
    <input id="maintenance-drawer" type="checkbox" onchange="cleanUrlParams()" class="drawer-toggle" />
    <div class="drawer-content">
        <!-- Page content here -->
        {{ $slot }}
    </div>
    <div class="drawer-side">
        <label for="maintenance-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="p-4 w-80 min-h-full bg-base-100 text-base-content">
            <!-- Drawer Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Add New Maintenance</h2>
                <label for="maintenance-drawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-mary-icon name="o-x-mark" class="w-5 h-5" />
                </label>
            </div>

            <!-- Maintenance Form -->
            <form class="space-y-3">
                <!-- Asset Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Asset</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option disabled selected>Select an asset</option>
                        <option>Laptop Dell XPS 13</option>
                        <option>Printer Canon MX490</option>
                        <option>Monitor Samsung 24"</option>
                    </select>
                </div>

                <!-- Maintenance Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Maintenance Type</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option disabled selected>Select maintenance type</option>
                        <option>Preventive</option>
                        <option>Corrective</option>
                        <option>Emergency</option>
                        <option>Routine</option>
                    </select>
                </div>

                <!-- Priority -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Priority</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option disabled selected>Select priority</option>
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                        <option>Critical</option>
                    </select>
                </div>

                <!-- Scheduled Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Scheduled Date</span>
                    </label>
                    <input type="date" class="w-full input input-bordered input-sm" />
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Description</span>
                    </label>
                    <textarea class="h-20 text-sm textarea textarea-bordered" placeholder="Describe the maintenance work needed..."></textarea>
                </div>

                <!-- Assigned Technician -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Assigned Technician</span>
                    </label>
                    <select class="w-full select select-bordered select-sm">
                        <option disabled selected>Select technician</option>
                        <option>John Doe</option>
                        <option>Jane Smith</option>
                        <option>Mike Johnson</option>
                    </select>
                </div>

                <!-- Estimated Cost -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Estimated Cost</span>
                    </label>
                    <input type="number" class="w-full input input-bordered input-sm" placeholder="0.00" step="0.01" />
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-3">
                    <button type="submit" class="flex-1 btn btn-primary btn-sm">
                        <x-mary-icon name="o-plus" class="mr-2 w-4 h-4" />
                        Create Maintenance
                    </button>
                    <label for="maintenance-drawer" class="flex-1 btn btn-outline btn-sm">
                        Cancel
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>