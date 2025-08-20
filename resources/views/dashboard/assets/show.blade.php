@extends('layouts.dashboard')

@section('title', 'Asset Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Asset Details</h1>
            <p class="text-gray-600 mt-1">{{ $asset->code }} - {{ $asset->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Asset
            </a>
            <a href="{{ route('assets.index') }}" class="btn btn-ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Assets
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset Information -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">Asset Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Asset Code</label>
                            <p class="text-lg font-mono bg-gray-100 px-3 py-2 rounded mt-1">{{ $asset->code }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Asset Name</label>
                            <p class="text-lg mt-1">{{ $asset->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Category</label>
                            <p class="text-lg mt-1">
                                <span class="badge badge-outline badge-lg">{{ $asset->category->name }}</span>
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Location</label>
                            <p class="text-lg mt-1">
                                <span class="badge badge-outline badge-lg">{{ $asset->location->name }}</span>
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Status</label>
                            <p class="text-lg mt-1">
                                @if($asset->status === 'active')
                                    <span class="badge badge-success badge-lg">Active</span>
                                @elseif($asset->status === 'inactive')
                                    <span class="badge badge-warning badge-lg">Inactive</span>
                                @elseif($asset->status === 'maintenance')
                                    <span class="badge badge-info badge-lg">Maintenance</span>
                                @else
                                    <span class="badge badge-error badge-lg">Disposed</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Condition</label>
                            <p class="text-lg mt-1">
                                @if($asset->condition === 'excellent')
                                    <span class="badge badge-success badge-lg">Excellent</span>
                                @elseif($asset->condition === 'good')
                                    <span class="badge badge-primary badge-lg">Good</span>
                                @elseif($asset->condition === 'fair')
                                    <span class="badge badge-warning badge-lg">Fair</span>
                                @else
                                    <span class="badge badge-error badge-lg">Poor</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Asset Value</label>
                            <p class="text-lg font-semibold text-green-600 mt-1">${{ number_format($asset->value, 2) }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Purchase Date</label>
                            <p class="text-lg mt-1">
                                {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'Not specified' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Created At</label>
                            <p class="text-lg mt-1">{{ $asset->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Last Updated</label>
                            <p class="text-lg mt-1">{{ $asset->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($asset->description)
                        <div class="mt-6">
                            <label class="text-sm font-semibold text-gray-600">Description</label>
                            <p class="text-lg mt-1 bg-gray-50 p-4 rounded-lg">{{ $asset->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline btn-block">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Asset
                        </a>
                        
                        <button class="btn btn-outline btn-warning btn-block" onclick="updateStatus('maintenance')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Mark as Maintenance
                        </button>
                        
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-error btn-block">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Asset
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Asset Stats -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">Asset Statistics</h3>
                    
                    <div class="space-y-4">
                        <div class="stat">
                            <div class="stat-title">Total Logs</div>
                            <div class="stat-value text-2xl">{{ $asset->logs->count() }}</div>
                            <div class="stat-desc">Activity records</div>
                        </div>
                        
                        <div class="stat">
                            <div class="stat-title">Days Since Purchase</div>
                            <div class="stat-value text-2xl">
                                {{ $asset->purchase_date ? $asset->purchase_date->diffInDays(now()) : 'N/A' }}
                            </div>
                            <div class="stat-desc">{{ $asset->purchase_date ? 'days old' : 'No purchase date' }}</div>
                        </div>
                        
                        <div class="stat">
                            <div class="stat-title">Last Activity</div>
                            <div class="stat-value text-lg">
                                {{ $asset->logs->first() ? $asset->logs->first()->created_at->diffForHumans() : 'No activity' }}
                            </div>
                            <div class="stat-desc">{{ $asset->logs->first() ? $asset->logs->first()->action : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="mt-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="card-title text-xl">Activity Log</h3>
                    <a href="{{ route('asset-logs.export', $asset) }}" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Log
                    </a>
                </div>
                
                @if($asset->logs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
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
                        <div class="text-center mt-4">
                            <a href="{{ route('asset-logs.index', ['asset' => $asset->id]) }}" class="btn btn-outline">
                                View All Logs ({{ $asset->logs->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
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