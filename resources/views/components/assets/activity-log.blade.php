@props(['asset', 'class' => ''])

<div class="shadow-xl card bg-base-100 {{ $class }}">
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