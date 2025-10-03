<!-- Vehicle Profile Alert Modal -->
<div 
    x-data="{ show: @entangle('showAlert') }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
    
    <!-- Modal container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="relative w-full max-w-md mx-auto bg-base-100 rounded-lg shadow-xl"
        >
            <!-- Modal content -->
            <div class="p-6">
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-warning/20 rounded-full">
                    <x-icon name="o-truck" class="w-6 h-6 text-warning" />
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-semibold text-center text-base-content mb-2">
                    Lengkapi Profil Kendaraan
                </h3>
                
                <!-- Message -->
                <p class="text-sm text-center text-base-content/70 mb-6">
                    Asset <strong>{{ $assetName }}</strong> berhasil dibuat sebagai kendaraan. 
                    Apakah Anda ingin melengkapi profil kendaraan sekarang?
                </p>
                
                <!-- Action buttons -->
                <div class="flex gap-3">
                    <button 
                        wire:click="cancelAlert"
                        class="flex-1 btn btn-ghost btn-sm"
                    >
                        Nanti Saja
                    </button>
                    <button 
                        wire:click="proceedToProfile"
                        class="flex-1 btn btn-primary btn-sm"
                    >
                        <x-icon name="o-arrow-right" class="w-4 h-4 mr-1" />
                        Lengkapi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>