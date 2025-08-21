@extends('layouts.dashboard')

@section('title', 'Asset Details')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('locations.index') }}" class="btn btn-ghost btn-sm">
                    <i data-lucide="arrow-left" class="mr-2 w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-base-content">Asset Details</h1>
                    <p class="mt-1 text-base-content/70">Informasi lengkap asset.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Asset Information -->
            <div class="lg:col-span-2">
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="mb-4 text-xl card-title">Asset Information</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Asset Code</label>
                                <p class="px-3 py-2 mt-1 font-mono text-lg bg-gray-100 rounded">{{ $asset->code }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Asset Name</label>
                                <p class="mt-1 text-lg">{{ $asset->name }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Category</label>
                                <p class="mt-1 text-lg">
                                    <span
                                        class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->category->name }}</span>
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Location</label>
                                <p class="mt-1 text-lg">
                                    <span
                                        class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->location->name }}</span>
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Status</label>
                                <p class="mt-1 text-lg">
                                    @if($asset->status === 'active')
                                        <span class="text-xs whitespace-nowrap badge badge-success">Active</span>
                                    @elseif($asset->status === 'inactive')
                                        <span class="text-xs whitespace-nowrap badge badge-warning">Inactive</span>
                                    @elseif($asset->status === 'maintenance')
                                        <span class="text-xs whitespace-nowrap badge badge-info">Maintenance</span>
                                    @else
                                        <span class="text-xs whitespace-nowrap badge badge-error">Disposed</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Condition</label>
                                <p class="mt-1 text-lg">
                                    @if($asset->condition === 'excellent')
                                        <span class="text-xs whitespace-nowrap badge badge-success">Excellent</span>
                                    @elseif($asset->condition === 'good')
                                        <span class="text-xs whitespace-nowrap badge badge-primary">Good</span>
                                    @elseif($asset->condition === 'fair')
                                        <span class="text-xs whitespace-nowrap badge badge-warning">Fair</span>
                                    @else
                                        <span class="text-xs whitespace-nowrap badge badge-error">Poor</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Asset Value</label>
                                <p class="mt-1 text-lg font-semibold text-green-600">${{ number_format($asset->value, 2) }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Purchase Date</label>
                                <p class="mt-1 text-lg">
                                    {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Created At</label>
                                <p class="mt-1 text-lg">{{ $asset->created_at->format('M d, Y H:i') }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Last Updated</label>
                                <p class="mt-1 text-lg">{{ $asset->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($asset->description)
                            <div class="mt-6">
                                <label class="text-sm font-semibold text-gray-600">Description</label>
                                <p class="p-4 mt-1 text-lg bg-gray-50 rounded-lg">{{ $asset->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Stats -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h3 class="mb-4 text-lg card-title">Quick Actions</h3>

                        <div class="space-y-3">
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline btn-block">
                                <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                                Edit Asset
                            </a>

                            <button class="btn btn-outline btn-warning btn-block" onclick="updateStatus('maintenance')">
                                <i data-lucide="settings" class="mr-2 w-4 h-4"></i>
                                Mark as Maintenance
                            </button>

                            <form method="POST" action="{{ route('assets.destroy', $asset) }}"
                                onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-error btn-block">
                                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                                    Delete Asset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Asset Stats -->
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h3 class="mb-4 text-lg card-title">Asset Statistics</h3>

                        <div class="space-y-4">
                            <div class="stat">
                                <div class="stat-title">Total Logs</div>
                                <div class="text-2xl stat-value">{{ $asset->logs->count() }}</div>
                                <div class="stat-desc">Activity records</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Days Since Purchase</div>
                                <div class="text-2xl stat-value">
                                    {{ $asset->purchase_date ? $asset->purchase_date->diffInDays(now()) : 'N/A' }}
                                </div>
                                <div class="stat-desc">{{ $asset->purchase_date ? 'days old' : 'No purchase date' }}</div>
                            </div>

                            <div class="stat">
                                <div class="stat-title">Last Activity</div>
                                <div class="text-lg stat-value">
                                    {{ $asset->logs->first() ? $asset->logs->first()->created_at->diffForHumans() : 'No activity' }}
                                </div>
                                <div class="stat-desc">{{ $asset->logs->first() ? $asset->logs->first()->action : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="mt-8">
            <div class="shadow-xl card bg-base-100">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl card-title">Activity Log</h3>
                        <a href="{{ route('asset-logs.export', $asset) }}" class="btn btn-outline btn-sm">
                            <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                            Export Log
                        </a>
                    </div>

                    @if($asset->logs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>Changes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->logs->take(10) as $log)
                                        <tr>
                                            <td>
                                                <div class="text-sm">
                                                    {{ $log->created_at->format('M d, Y') }}
                                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i') }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-outline">{{ ucfirst($log->action) }}</span>
                                            </td>
                                            <td>{{ $log->user->name }}</td>
                                            <td>
                                                @if($log->changed_fields)
                                                    <div class="text-sm">
                                                        @foreach(json_decode($log->changed_fields, true) as $field => $change)
                                                            <div class="mb-1">
                                                                <strong>{{ ucfirst($field) }}:</strong>
                                                                <span class="text-red-500">{{ $change['old'] ?? 'N/A' }}</span>
                                                                →
                                                                <span class="text-green-500">{{ $change['new'] ?? 'N/A' }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">No changes recorded</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($asset->logs->count() > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('asset-logs.index', ['asset' => $asset->id]) }}" class="btn btn-outline">
                                    View All Logs ({{ $asset->logs->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="py-8 text-center">
                            <i data-lucide="file-text" class="block mx-auto mb-3 w-12 h-12 text-gray-400"></i>
                            <p class="text-gray-500">No activity logs found for this asset.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(status) {
            if (confirm(`Are you sure you want to mark this asset as ${status}?`)) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("assets.update-status", $asset) }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = status;

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(statusField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection