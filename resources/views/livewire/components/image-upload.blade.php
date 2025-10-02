<div class="py-0 fieldset">
    <legend class="mb-0.5 fieldset-legend">
        {{ $label }}
    </legend>

    <!-- Image Preview Section with Integrated Upload -->
    <div class="flex gap-2">
        <!-- Upload Area with integrated file input -->
        <div class="relative w-16 h-16">
            <label
                class="flex flex-col justify-center items-center w-full h-full rounded-lg border-2 border-dashed transition-colors cursor-pointer bg-base-200 text-base-content/60 border-base-300 hover:bg-base-300">
                @if ($currentImage || $tempImagePath)
                    <x-avatar :image="$currentImage ? $this->getCurrentImageUrl() : $this->getTempImageUrl()"
                        class="!w-full !rounded-lg !bg-primary !font-bold border-2 border-base-100" />
                @else
                    <x-icon name="o-cloud-arrow-up" class="mb-1 w-8 h-8" />
                    <span class="text-xs text-center">Unggah</span>
                @endif
                <input type="file" wire:model.live="imageFile" accept="{{ $accept }}"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
            </label>
        </div>

        @if ($currentImage || $tempImagePath)
            <x-button type="button" wire:click="removeImage" class="btn-xs text-error">
                <x-icon name="o-trash" class="w-4 h-4" />
            </x-button>
        @endif
    </div>


    <!-- Loading Indicator -->
    <div wire:loading wire:target="imageFile" class="mt-2">
        <div class="flex gap-2 items-center text-sm text-base-content/60">
            <span class="loading loading-spinner loading-xs"></span>
            Mengupload gambar...
        </div>
    </div>

    <!-- Hint -->
    <div class="mt-1 fieldset-label">{{ $hint }}</div>

    <!-- Error Messages -->
    @error('imageFile')
        <div class="mt-1 text-sm text-error">{{ $message }}</div>
    @enderror
</div>