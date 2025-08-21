@props(['logs', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
  <table class="table table-striped table-hover">
    <thead class="table-dark">
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
      <div class="text-sm text-gray-500">{{ $log->asset->code }}</div>
      </div>
      @else
      <span class="text-gray-400">Asset Deleted</span>
      @endif
      </td>
      <td>
        <span class="badge {{ $log->actionBadgeColor }}">
        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
        </span>
      </td>
      <td>
        @if($log->user)
      {{ $log->user->name }}
      @else
      <span class="text-gray-400">System</span>
      @endif
      </td>
      <td>
        <div class="max-w-xs truncate" title="{{ $log->formattedChanges }}">
        {{ $log->formattedChanges }}
        </div>
      </td>
      <td>{{ $log->created_at->format('d M Y H:i') }}</td>
      <td>
        <button class="btn btn-sm btn-ghost" popovertarget="log-dropdown-{{ $log->id }}"
        style="anchor-name: --log-anchor-{{ $log->id }}">
        <i data-lucide="more-vertical" class="w-4 h-4"></i>
        </button>
        <ul class="w-52 border shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100" popover
        id="log-dropdown-{{ $log->id }}" style="position-anchor: --log-anchor-{{ $log->id }}">
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
      <td colspan="6" class="py-8 text-center">
        <div class="flex flex-col justify-center items-center space-y-2">
        <i data-lucide="file-text" class="w-12 h-12 text-gray-400"></i>
        <p class="text-gray-500">No asset logs found</p>
        </div>
      </td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>