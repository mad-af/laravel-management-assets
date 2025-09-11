@props(['logs', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Asset</th>
                <th>Action</th>
                <th>User</th>
                <th>Changes</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr data-log-id="{{ $log->id }}">
                    <td>
                        @if($log->asset)
                            <div>
                                <div class="font-medium">{{ $log->asset->name }}</div>
                                <div class="text-sm text-base-content/60">{{ $log->asset->code }}</div>
                            </div>
                        @else
                            <span class="text-base-content/60">Asset Deleted</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                \App\Enums\AssetLogAction::CREATED->value => 'badge-success',
                                \App\Enums\AssetLogAction::UPDATED->value => 'badge-info',
                                \App\Enums\AssetLogAction::DELETED->value => 'badge-error',
                                \App\Enums\AssetLogAction::STATUS_CHANGED->value => 'badge-warning'
                            ];
                            $colorClass = $actionColors[$log->action] ?? 'badge-neutral';
                        @endphp
                        <span class="badge {{ $colorClass }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td>
                        @if($log->user)
                            {{ $log->user->name }}
                        @else
                            <span class="text-base-content/60">System</span>
                        @endif
                    </td>
                    <td>
                        @if($log->changed_fields)
                            <div class="max-w-xs text-sm">
                                @php
                                    $changedFields = is_string($log->changed_fields)
                                        ? json_decode($log->changed_fields, true)
                                        : $log->changed_fields;
                                @endphp
                                @if(is_array($changedFields))
                                    @foreach($changedFields as $field => $change)
                                        <div class="mb-1 truncate">
                                            <strong>{{ ucfirst($field) }}:</strong>
                                            <span class="text-error">{{ $change['old'] ?? 'N/A' }}</span>
                                            →
                                            <span class="text-success">{{ $change['new'] ?? 'N/A' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-base-content/60">Invalid change data format</span>
                                @endif
                            </div>
                        @else
                            <span class="text-base-content/60">No changes recorded</span>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <button class="btn btn-sm btn-ghost" popovertarget="log-dropdown-{{ $log->id }}"
                            style="anchor-name: --log-anchor-{{ $log->id }}">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <ul class="w-52 border shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100"
                            popover id="log-dropdown-{{ $log->id }}"
                            style="position-anchor: --log-anchor-{{ $log->id }}">
                            <li>
                                <a href="{{ route('asset-logs.show', $log) }}" onclick="document.activeElement.blur()">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View Details
                                </a>
                            </li>
                            @if($log->asset)
                                <li>
                                    <a href="{{ route('assets.show', $log->asset) }}" onclick="document.activeElement.blur()">
                                        <i data-lucide="package" class="w-4 h-4"></i>
                                        View Asset
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @empty
                 <tr>
                     <td colspan="6" class="py-4 text-center text-base-content/60">
                         <i data-lucide="file-text" class="block mx-auto mb-3 w-12 h-12 opacity-50"></i>
                         No asset logs found
                     </td>
                 </tr>
            @endforelse
        </tbody>
    </table>
</div>