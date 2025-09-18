<!-- Card Table List AssetTransferItem -->
<div class="shadow-xl card card-compact bg-base-100">
    <div class="card-body">
        <h2 class="text-lg card-title">
            <x-icon name="o-cube" class="w-5 h-5" />
            Transfer Items ({{ count($itemsData) }})
        </h2>

        <div class="mt-4">
            @php
                $headers = [
                    ['key' => 'asset_code', 'label' => 'Asset Code', 'class' => 'font-mono font-bold'],
                    ['key' => 'asset_info', 'label' => 'Asset'],
                    ['key' => 'from_location', 'label' => 'From Location'],
                    ['key' => 'to_location', 'label' => 'To Location'],
                    ['key' => 'current_location', 'label' => 'Current Location']
                ];
            @endphp

            @if(count($itemsData) > 0)
                <x-table :headers="$headers" :rows="collect($itemsData)" striped>
                    @scope('cell_asset_code', $item)
                    <span class="font-mono font-normal">{{ $item['asset_code'] ?? 'N/A' }}</span>
                    @endscope

                    @scope('cell_asset_info', $item)
                    <div>
                        {{ $item['asset_name'] ?? 'N/A' }}
                        <div class="text-sm text-base-content/70">
                            {{ trim(($item['asset_brand'] ?? '') . ' ' . ($item['asset_model'] ?? '')) }}</div>
                    </div>
                    @endscope

                    @scope('cell_from_location', $item)
                    {{ $item['from_location'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_to_location', $item)
                    {{ $item['to_location'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_current_location', $item)
                    {{ $item['current_location'] ?? 'N/A' }}
                    @endscope
                </x-table>
            @else
                <div class="py-8 text-center text-base-content/70">
                    <x-icon name="o-cube" class="mx-auto mb-2 w-12 h-12 text-base-content/30" />
                    <p>No items found</p>
                </div>
            @endif
        </div>
    </div>
</div>