<!-- Card Table List AssetTransferItem -->
<div class="card card-compact bg-base-100 shadow-xl">
    <div class="card-body">
        <h2 class="card-title text-lg">
            <x-icon name="o-cube" class="w-5 h-5" />
            Transfer Items ({{ count($itemsData) }})
        </h2>
        
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Asset Code</th>
                        <th>From Location</th>
                        <th>To Location</th>
                        <th>Current Location</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsData as $item)
                    <tr class="hover">
                        <td>
                            <div class="font-semibold">{{ $item['asset_name'] ?? 'N/A' }}</div>
                            <div class="text-sm text-base-content/70">{{ $item['asset_brand'] ?? '' }} {{ $item['asset_model'] ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-outline">{{ $item['asset_code'] ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $item['from_location'] ?? 'N/A' }}</td>
                        <td>{{ $item['to_location'] ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $item['current_location'] ?? 'Unknown' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-base-content/70 py-8">
                            <x-icon name="o-cube" class="w-12 h-12 mx-auto mb-2 text-base-content/30" />
                            <p>No items found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>