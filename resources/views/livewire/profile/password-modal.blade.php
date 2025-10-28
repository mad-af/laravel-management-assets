<!-- Password Reset Modal -->
<div 
    x-data="{ show: @entangle('show') }"
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
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="$wire.closeModal()"></div>
    
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
                <h2 class="mb-4 text-lg font-semibold">Ubah Password</h2>

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password Saat Ini</span>
                        </label>
                        <input type="password" class="input input-bordered" wire:model.defer="current_password" />
                        @error('current_password')
                            <span class="mt-1 text-sm text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password Baru</span>
                        </label>
                        <input type="password" class="input input-bordered" wire:model.defer="password" />
                        @error('password')
                            <span class="mt-1 text-sm text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Konfirmasi Password Baru</span>
                        </label>
                        <input type="password" class="input input-bordered" wire:model.defer="password_confirmation" />
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <x-button class="btn-ghost" label="Batal" type="button" onclick="Livewire.dispatch('dismiss-password-modal')" />
                        <x-button class="btn-primary" label="Update Password" type="submit" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>