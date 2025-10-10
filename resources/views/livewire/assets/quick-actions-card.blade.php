<x-info-card title="Quick Actions" icon="o-bolt">
    <div class="space-y-2">
        <!-- Print QR Code -->
        <button wire:click="printQRBarcode" class="w-full btn btn-sm">
            <x-icon name="o-qr-code" class="w-4 h-4" />
            Print QR Code
        </button>
        
        <!-- Edit Asset -->
        <button wire:click="editAsset" class="w-full btn btn-sm">
            <x-icon name="o-pencil" class="w-4 h-4" />
            Edit Asset
        </button>

        <!-- View Asset Logs -->
        {{-- <button wire:click="viewAssetLogs" class="w-full btn btn-sm">
            <x-icon name="o-document-text" class="w-4 h-4" />
            View Logs
        </button> --}}

        <!-- Create Maintenance -->
        <a href="{{ route('maintenances.index', ['asset_id' => $asset->id]) }}" class="w-full btn btn-sm">
            <x-icon name="o-wrench-screwdriver" class="w-4 h-4" />
            Perawatan Asset
        </a>

        <!-- Create Transfer -->
        {{-- <a href="{{ route('asset-transfers.index', ['asset_id' => $asset->id]) }}" class="w-full btn btn-sm">
            <x-icon name="o-arrows-right-left" class="w-4 h-4" />
            Transfer Asset
        </a> --}}

        <!-- Create Loan -->
        {{-- <a href="{{ route('asset-loans.index', ['asset_id' => $asset->id]) }}" class="w-full btn btn-sm">
            <x-icon name="o-hand-raised" class="w-4 h-4" />
            Loan Asset
        </a> --}}

        <!-- Delete Asset -->
        {{-- <button wire:click="deleteAsset" 
                wire:confirm="Apakah Anda yakin ingin menghapus asset ini? Tindakan ini tidak dapat dibatalkan."
                class="w-full btn btn-sm btn-error btn-outline">
            <x-icon name="o-trash" class="w-4 h-4" />
            Delete Asset
        </button> --}}
    </div>
</x-info-card>