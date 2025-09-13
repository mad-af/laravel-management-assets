<div class="drawer drawer-end z-50">
    <input id="maintenance-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
        <!-- Page content here -->
        {{ $slot }}
    </div>
    <div class="drawer-side">
        <label for="maintenance-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <div class="bg-base-100 text-base-content min-h-full w-80 p-4">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between mb-4">
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
                        <span class="label-text font-medium">Asset</span>
                    </label>
                    <select class="select select-bordered select-sm w-full">
                        <option disabled selected>Select an asset</option>
                        <option>Laptop Dell XPS 13</option>
                        <option>Printer Canon MX490</option>
                        <option>Monitor Samsung 24"</option>
                    </select>
                </div>

                <!-- Maintenance Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Maintenance Type</span>
                    </label>
                    <select class="select select-bordered select-sm w-full">
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
                        <span class="label-text font-medium">Priority</span>
                    </label>
                    <select class="select select-bordered select-sm w-full">
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
                        <span class="label-text font-medium">Scheduled Date</span>
                    </label>
                    <input type="date" class="input input-bordered input-sm w-full" />
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Description</span>
                    </label>
                    <textarea class="textarea textarea-bordered h-20 text-sm" placeholder="Describe the maintenance work needed..."></textarea>
                </div>

                <!-- Assigned Technician -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Assigned Technician</span>
                    </label>
                    <select class="select select-bordered select-sm w-full">
                        <option disabled selected>Select technician</option>
                        <option>John Doe</option>
                        <option>Jane Smith</option>
                        <option>Mike Johnson</option>
                    </select>
                </div>

                <!-- Estimated Cost -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Estimated Cost</span>
                    </label>
                    <input type="number" class="input input-bordered input-sm w-full" placeholder="0.00" step="0.01" />
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-3">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <x-mary-icon name="o-plus" class="w-4 h-4 mr-2" />
                        Create Maintenance
                    </button>
                    <label for="maintenance-drawer" class="btn btn-outline btn-sm flex-1">
                        Cancel
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>