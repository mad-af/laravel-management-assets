@props(['assets', 'class' => ''])

<div class="overflow-x-auto {{ $class }}">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr data-asset-id="{{ $asset->id }}" data-asset-code="{{ $asset->code }}"
                    data-asset-name="{{ $asset->name }}">
                    <td>
                        <span class="font-mono text-sm">{{ $asset->code }}</span>
                    </td>
                    <td>
                        <div class="font-medium">{{ $asset->name }}</div>
                        @if($asset->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($asset->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="min-w-0">
                        <span
                            class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->category->name ?? 'N/A' }}</span>
                    </td>
                    <td class="min-w-0">
                        <span
                            class="text-xs whitespace-nowrap badge badge-outline">{{ $asset->location->name ?? 'N/A' }}</span>
                    </td>
                    <td class="min-w-0">
                        <span
                            class="badge {{ $asset->status_badge_color }} text-xs whitespace-nowrap">{{ ucfirst($asset->status) }}</span>
                    </td>
                    <td class="min-w-0">
                        <span
                            class="badge {{ $asset->condition_badge_color }} text-xs whitespace-nowrap">{{ ucfirst($asset->condition) }}</span>
                    </td>
                    <td>
                        <span class="font-medium">{{ $asset->formatted_value }}</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-ghost" popovertarget="asset-dropdown-{{ $asset->id }}"
                            style="anchor-name: --asset-anchor-{{ $asset->id }}">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <ul class="w-52 shadow-lg dropdown dropdown-left dropdown-center menu rounded-box bg-base-100" popover
                            id="asset-dropdown-{{ $asset->id }}" style="position-anchor: --asset-anchor-{{ $asset->id }}">
                            <li>
                                <a href="{{ route('assets.show', $asset) }}" onclick="document.activeElement.blur()">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('assets.edit', $asset) }}" onclick="document.activeElement.blur()">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    Edit
                                </a>
                            </li>
                            <li>
                                <a onclick="printQRBarcode('{{ $asset->code }}', '{{ $asset->name }}'); document.activeElement.blur()">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    Print QR/Barcode
                                </a>
                            </li>
                            <li>
                                <a onclick="deleteAsset({{ $asset->id }}); document.activeElement.blur()" class="text-error">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Delete
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-4 text-center text-muted">
                        <i data-lucide="package" class="block mx-auto mb-3 w-12 h-12"></i>
                        No assets found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>