@props(['locations', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locations as $location)
                <tr data-location-id="{{ $location->id }}" data-location-name="{{ $location->name }}">
                    <td>{{ $location->id }}</td>
                    <td>{{ $location->name }}</td>
                    <td>
                        @if($location->is_active)
                            <span class="text-xs whitespace-nowrap badge badge-success">Active</span>
                        @else
                            <span class="text-xs whitespace-nowrap badge badge-error">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $location->created_at->format('d M Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-ghost" popovertarget="location-dropdown-{{ $location->id }}"
                            style="anchor-name: --location-anchor-{{ $location->id }}">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <ul class="w-52 shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100"
                            popover id="location-dropdown-{{ $location->id }}"
                            style="position-anchor: --location-anchor-{{ $location->id }}">
                            @if($location->is_active)
                                <li>
                                    <a href="{{ route('locations.show', $location) }}" onclick="document.activeElement.blur()">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('locations.edit', $location) }}" onclick="document.activeElement.blur()">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="deleteLocation('{{ $location->id }}'); document.activeElement.blur()"
                                        class="text-error">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Deactivate
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a onclick="activateLocation('{{ $location->id }}')" class="text-success">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Activate
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-muted">
                        <i data-lucide="map-pin" class="block mx-auto mb-3 w-12 h-12"></i>
                        No locations found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>