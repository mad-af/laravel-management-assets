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

        <!-- Download Activity Log -->
        <button wire:click="downloadActivityLog" class="w-full btn btn-sm">
            <x-icon name="o-document-arrow-down" class="w-4 h-4" />
            Unduh Aktivitas Asset
        </button>

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

        @if($this->isAdmin)
            @if($asset->status === \App\Enums\AssetStatus::INACTIVE)
                <!-- Activate Asset -->
                <button wire:click="openActivateConfirm" class="w-full btn btn-sm btn-success">
                    <x-icon name="o-check" class="w-4 h-4" />
                    Aktifkan Asset
                </button>
            @elseif ($asset->status === \App\Enums\AssetStatus::ACTIVE)
                <!-- Deactivate Asset -->
                <button wire:click="openDeactivateConfirm" class="w-full btn btn-sm btn-error btn-outline">
                    <x-icon name="o-no-symbol" class="w-4 h-4" />
                    Nonaktifkan Asset
                </button>
            @endif
        @endif
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirm)
        <div class="fixed inset-0 z-50" style="display: block;">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="$set('showConfirm', false)"></div>

            <!-- Modal -->
            <div class="flex justify-center items-center px-4 py-6 min-h-screen">
                <div class="relative mx-auto w-full max-w-md rounded-lg shadow-xl bg-base-100">
                    <div class="p-6">
                        <div class="flex gap-3 items-start">
                            <div class="flex flex-shrink-0 justify-center items-center mt-0.5 w-10 h-10 rounded-full bg-info/20">
                                <x-icon name="o-information-circle" class="w-6 h-6 text-info" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold">Konfirmasi Aksi</h4>
                                <p class="mt-1 text-sm text-base-content/80">
                                    Untuk melanjutkan, ketik frasa konfirmasi yang diminta.
                                </p>

                                @include('livewire.components.confirmation-phrase', ['phrase' => $confirmationPhrase])

                                <div class="flex gap-2 justify-end mt-4">
                                    <x-button label="Batal" class="btn-ghost btn-sm" wire:click="$set('showConfirm', false)" />
                                    <x-button label="Konfirmasi" class="btn-info btn-sm" wire:click="confirmStatusChange" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-info-card>