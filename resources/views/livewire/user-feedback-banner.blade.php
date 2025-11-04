<div>
    <!-- Banner -->
    <div x-data="{ open: @entangle('showBanner') }" x-show="open" class="p-4 rounded-lg border-2 border-dashed border-secondary border-base-300 bg-base-100">
        <div class="flex gap-3 justify-between items-center">
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-8 h-8 rounded-full bg-secondary/20">
                    <x-icon name="o-chat-bubble-left-right" class="w-5 h-5 text-secondary" />
                </div>
                <div>
                    <p class="text-sm font-medium">Bantu kami meningkatkan aplikasi ini</p>
                    <p class="text-xs text-base-content/70">Periode: {{ $period }} · Berikan rating dan masukan singkat Anda.</p>
                </div>
            </div>
            <div class="flex gap-2 items-center">
                <button class="btn btn-secondary btn-sm" wire:click="openModal">
                    Beri Feedback
                </button>
                <button class="btn btn-ghost btn-sm" @click="open=false">Nanti</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div 
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="overflow-y-auto fixed inset-0 z-50"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>

        <div class="flex justify-center items-center px-4 py-6 min-h-screen">
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="relative mx-auto w-full max-w-md rounded-lg shadow-xl bg-base-100"
            >
                <div class="p-6 space-y-4">
                    <div class="flex justify-center items-center mx-auto mb-2 w-12 h-12 rounded-full bg-secondary/20">
                        <x-icon name="o-star" class="w-6 h-6 text-secondary" />
                    </div>
                    <h3 class="text-lg font-semibold text-center">Feedback Pengguna</h3>

                    <form wire:submit.prevent="submit" class="space-y-4">
                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-medium">Rating</label>
                            <div class="mt-2 rating">
                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-warning" aria-label="1 star" value="1" wire:model="rating" />
                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-warning" aria-label="2 star" value="2" wire:model="rating" />
                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-warning" aria-label="3 star" value="3" wire:model="rating" />
                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-warning" aria-label="4 star" value="4" wire:model="rating" />
                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-warning" aria-label="5 star" value="5" wire:model="rating" />
                            </div>
                            @error('rating')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="text-sm font-medium">Masukan / Request</label>
                            <textarea class="mt-2 w-full textarea textarea-bordered" rows="4" placeholder="Tulis masukan singkat Anda" wire:model.defer="message"></textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="button" class="flex-1 btn btn-ghost btn-sm" wire:click="closeModal">Batal</button>
                            <button type="submit" class="flex-1 btn btn-secondary btn-sm">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>