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
                <h2 id="drawer-title" class="text-lg font-semibold">Add New Maintenance</h2>
                <label for="maintenance-drawer" class="btn btn-sm btn-circle btn-ghost">
                    <x-icon name="o-x-mark" class="w-5 h-5" />
                </label>
            </div>

            <!-- Maintenance Form -->
            <form id="maintenance-form" action="/admin/maintenances" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" id="maintenance-id" name="maintenance_id" value="">
                <input type="hidden" id="form-method" name="_method" value="">
                <!-- Asset Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Asset <span class="text-red-500">*</span></span>
                    </label>
                    <select id="asset-id" name="asset_id" class="w-full select select-bordered select-sm" required>
                        <option disabled selected>Select an asset</option>
                        @php
                            $assets = \App\Models\Asset::with('category')->where('status', '!=', \App\Enums\AssetStatus::LOST)->orderBy('name')->get();
                        @endphp
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }}) -
                                {{ $asset->category->name ?? 'No Category' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Title <span class="text-red-500">*</span></span>
                    </label>
                    <input id="maintenance-title" type="text" name="title" class="w-full input input-bordered input-sm"
                        placeholder="Enter maintenance title" required />
                </div>

                <!-- Maintenance Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Maintenance Type <span
                                class="text-red-500">*</span></span>
                    </label>
                    <select id="maintenance-type" name="type" class="w-full select select-bordered select-sm" required>
                        <option disabled selected>Select maintenance type</option>
                        @foreach(\App\Enums\MaintenanceType::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Priority <span class="text-red-500">*</span></span>
                    </label>
                    <select id="maintenance-priority" name="priority" class="w-full select select-bordered select-sm"
                        required>
                        <option disabled selected>Select priority</option>
                        @foreach(\App\Enums\MaintenancePriority::cases() as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Status</span>
                    </label>
                    <select id="maintenance-status" name="status" class="w-full select select-bordered select-sm">
                        @foreach(\App\Enums\MaintenanceStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $status->value === 'open' ? 'selected' : '' }}>
                                {{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Scheduled Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Scheduled Date</span>
                    </label>
                    <input id="maintenance-scheduled-date" type="date" name="scheduled_date"
                        class="w-full input input-bordered input-sm" />
                </div>

                <!-- Description -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Description <span
                                class="text-red-500">*</span></span>
                    </label>
                    <textarea id="maintenance-description" name="description"
                        class="h-20 text-sm textarea textarea-bordered"
                        placeholder="Describe the maintenance work needed..." required></textarea>
                </div>

                <!-- Assigned Technician -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Assigned Technician</span>
                    </label>
                    <select id="maintenance-assigned-to" name="assigned_to"
                        class="w-full select select-bordered select-sm">
                        <option value="" selected>Select technician (optional)</option>
                        @php
                            $users = \App\Models\User::orderBy('name')->get();
                        @endphp
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->value }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estimated Cost -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Estimated Cost</span>
                    </label>
                    <input id="maintenance-cost" type="number" name="cost" class="w-full input input-bordered input-sm"
                        placeholder="0.00" step="0.01" min="0" />
                </div>

                <!-- Notes -->
                <div class="form-control">
                    <label class="label">
                        <span class="text-xs font-bold label-text">Notes</span>
                    </label>
                    <textarea id="maintenance-notes" name="notes" class="h-16 text-sm textarea textarea-bordered"
                        placeholder="Additional notes or comments..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-3">
                    <button id="submit-btn" type="submit" class="flex-1 btn btn-primary btn-sm">
                        <x-icon id="submit-icon" name="o-plus" class="mr-2 w-4 h-4" />
                        <span id="submit-text">Create Maintenance</span>
                    </button>
                    <label for="maintenance-drawer" class="flex-1 btn btn-outline btn-sm">
                        Cancel
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>