<div class="relative">
    <fieldset class="relative py-0 fieldset">
        @if($label)
            <legend class="mb-0.5 fieldset-legend">
                {{ $label }}
                @isset($required)
                    @if($required)
                        <span class="text-error">*</span>
                    @endif
                @endisset
            </legend>
        @endif
            
        <label class="relative w-full input {{ $class }}">
            <div class="z-50 tooltip">
                <div class="text-xs tooltip-content">
                    {{ collect($selected)->pluck($optionLabel)->join(', ') }}
                </div>
                @if ($selected && count($selected) == 1)
                    <x-badge class="badge-sm badge-outline"
                        value="{{ \Illuminate\Support\Str::limit((string) data_get($selected->first(), $optionLabel), 16) }}" />
                @elseif($selected && count($selected) > 1)
                    <x-badge class="badge-sm badge-outline" value="{{ count($selected) - 1 }}" />
                    <x-badge class="badge-sm badge-outline"
                        value="{{ \Illuminate\Support\Str::limit((string) data_get($selected->last(), $optionLabel), 16) }}" />
                @endif
            </div>
            <input type="search" class="z-50 w-full min-w-0"
                placeholder="{{ $selected && count($selected) ? '' : $placeholder }}"
                wire:model.live.debounce.600ms="search" wire:focus="$set('showDropdown', true)"
                wire:keydown.escape="$set('showDropdown', false)" @if($disabled) disabled @endif />
            <div class="z-50">
                @if($clearable && ($search || $value))
                    <button type="button" class="btn btn-ghost btn-square btn-xs" wire:click.stop="clear">
                        <x-icon name="o-x-mark" class="!h-4 text-error/80" />
                    </button>
                @endif
                <x-icon name="o-chevron-up-down" class="!h-4" />
            </div>
        </label>
    </fieldset>

    {{-- Overlay untuk click di luar component --}}
    @if($showDropdown)
        <div class="fixed inset-0 z-40" wire:click="$set('showDropdown', false)"></div>
    @endif

    {{-- @if($showDropdown) --}}
    <ul
        class="absolute left-0 right-0 top-full mt-1 shadow-md list bg-base-100 overflow-auto max-h-60 rounded-box {{ $showDropdown ? '' : 'hidden' }} z-[60]">
        <li class="p-4 pb-2 text-xs tracking-wide opacity-60">{{ $headerText }}</li>
        @if($isLoading)
            <li class="flex gap-2 items-center px-4 py-2 text-sm text-base-content/70">
                <span class="loading loading-spinner loading-xs"></span>
                Mencari...
            </li>
        @else
            @forelse($options as $opt)
                @php
                    $optLabel = data_get($opt, $optionLabel);
                    $optValue = data_get($opt, $optionValue);
                    $optAvatar = $optionAvatar ? data_get($opt, $optionAvatar) : null;
                    $optSubLabel = $optionSubLabel ? data_get($opt, $optionSubLabel) : null;
                    $optMeta = $optionMeta ? data_get($opt, $optionMeta) : null;
                    $avatarUrl = null;
                    if ($optAvatar) {
                        $avatarUrl = str_starts_with((string) $optAvatar, 'http')
                            ? (string) $optAvatar
                            : asset('storage/' . $optAvatar);
                    }
                @endphp

                @if(!$multiple)
                    <li class="">
                        @php
                            $isSelected = (string) $value !== '' && (string) $value === (string) $optValue;
                        @endphp
                        <label
                            class="list-row py-2.5 items-center cursor-pointer hover:bg-base-300/60 {{ $isSelected ? 'bg-base-300/60' : '' }}">
                            <input type="radio" name="{{ $id }}" class="sr-only" wire:model.live="value" value="{{ $optValue }}" />
                            <div class="grow">
                                <div class="flex gap-2 items-center">
                                    @if($avatarUrl)
                                        <x-avatar :image="$avatarUrl"
                                            class="!w-10 !h-10 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100" />
                                    @elseif($optionAvatar)
                                        <div
                                            class="flex justify-center items-center font-bold rounded-lg border-2 size-10 bg-base-300 border-base-100">
                                            <x-icon name="o-photo" class="w-5 h-5 text-base-content/60" />
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        @if($optMeta)
                                            <div class="text-xs truncate text-base-content/60">{{ $optMeta }}</div>
                                        @endif
                                        <div class="font-medium truncate">{{ $optLabel }}</div>
                                        @if($optSubLabel)
                                            <div class="text-xs truncate text-base-content/70">{{ $optSubLabel }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    </li>
                @else
                    <li class="">
                        @php
                            $isSelected = is_array($value)
                                ? in_array((string) $optValue, array_map('strval', $value), true)
                                : ((string) $value !== '' && (string) $value === (string) $optValue);
                        @endphp
                        <label
                            class="list-row items-center py-2.5 cursor-pointer hover:bg-base-300/60 {{ $isSelected ? 'bg-base-300/60' : '' }}"
                            wire:click.stop="$set('showDropdown', true)" wire:mousedown.stop="$set('showDropdown', true)">
                            <input type="checkbox" name="{{ $id }}-{{ $optValue }}" class="mr-2 checkbox checkbox-sm"
                                wire:model.live="value" value="{{ $optValue }}" />
                            <div class="grow">
                                <div class="flex gap-2 items-center">
                                    @if($avatarUrl)
                                        <x-avatar :image="$avatarUrl"
                                            class="!w-10 !h-10 !rounded-lg !bg-base-300 !font-bold border-2 border-base-100" />
                                    @elseif($optionAvatar)
                                        <div
                                            class="flex justify-center items-center font-bold rounded-lg border-2 size-10 bg-base-300 border-base-100">
                                            <x-icon name="o-photo" class="w-5 h-5 text-base-content/60" />
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        @if($optMeta)
                                            <div class="text-xs truncate text-base-content/60">{{ $optMeta }}</div>
                                        @endif
                                        <div class="font-medium truncate">{{ $optLabel }}</div>
                                        @if($optSubLabel)
                                            <div class="text-xs truncate text-base-content/70">{{ $optSubLabel }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    </li>
                @endif
            @empty
                <li class="p-4 text-sm opacity-60">{{ $emptyText }}</li>
            @endforelse
        @endif
    </ul>
    {{-- @endif --}}
</div>