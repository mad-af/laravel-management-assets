<div class="space-y-6">
    <!-- Filters Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">Filter Logs</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Action Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Action</span>
                    </label>
                    <select wire:model.live="actionFilter" class="select select-bordered select-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- User Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">User</span>
                    </label>
                    <select wire:model.live="userFilter" class="select select-bordered select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">From Date</span>
                    </label>
                    <input wire:model.live="dateFromFilter" type="date" class="input input-bordered input-sm" />
                </div>

                <!-- Date To Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">To Date</span>
                    </label>
                    <input wire:model.live="dateToFilter" type="date" class="input input-bordered input-sm" />
                </div>
            </div>

            <!-- Clear Filters Button -->
            <div class="mt-4">
                <button wire:click="clearFilters" class="btn  btn-sm">
                    <i data-lucide="x" class="mr-2 w-4 h-4"></i>
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Changes</th>
                                <th>Notes</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @php
                                                $actionConfig = [
                                                    'created' => ['icon' => 'plus-circle', 'class' => 'text-success'],
                                                    'updated' => ['icon' => 'edit', 'class' => 'text-info'],
                                                    'deleted' => ['icon' => 'trash-2', 'class' => 'text-error'],
                                                    'status_changed' => ['icon' => 'refresh-cw', 'class' => 'text-warning'],
                                                    'checked_out' => ['icon' => 'log-out', 'class' => 'text-primary'],
                                                    'checked_in' => ['icon' => 'log-in', 'class' => 'text-success'],
                                                    'scanned' => ['icon' => 'scan', 'class' => 'text-info'],
                                                ];
                                                $config = $actionConfig[$log->action] ?? ['icon' => 'activity', 'class' => 'text-base-content'];
                                            @endphp
                                            <i data-lucide="{{ $config['icon'] }}" class="w-4 h-4 {{ $config['class'] }}"></i>
                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <span>{{ $log->user->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->changed_fields)
                                            <div class="text-sm">
                                                @foreach($log->changed_fields as $field => $changes)
                                                    <div class="mb-1">
                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                        <span class="text-error">{{ $changes['old'] ?? 'N/A' }}</span>
                                                        <i data-lucide="arrow-right" class="w-3 h-3 inline mx-1"></i>
                                                        <span class="text-success">{{ $changes['new'] ?? 'N/A' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-base-content/50">No changes</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->notes)
                                            <div class="tooltip" data-tip="{{ $log->notes }}">
                                                <span
                                                    class="text-sm truncate max-w-xs block">{{ Str::limit($log->notes, 50) }}</span>
                                            </div>
                                        @else
                                            <span class="text-base-content/50">No notes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                                            <div class="text-base-content/70">{{ $log->created_at->format('H:i:s') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            <a href="{{ route('asset-logs.show', $log) }}" class="btn btn-ghost btn-xs">
                                                <i data-lucide="eye" class="w-3 h-3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="file-text" class="w-16 h-16 mx-auto text-base-content/30 mb-4"></i>
                    <h3 class="text-lg font-semibold text-base-content/70 mb-2">No Logs Found</h3>
                    <p class="text-base-content/50">No activity logs found for this asset with the current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>