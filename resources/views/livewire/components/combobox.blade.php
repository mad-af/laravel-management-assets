<div>
    <fieldset class="py-0 fieldset">
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

        <label class="w-full input" wire:click="$set('showDropdown', true)">
            <input
                type="search"
                class="grow"
                placeholder="{{ $placeholder }}"
                wire:model.live="search"
                wire:keydown.escape="$set('showDropdown', false)"
                @if($disabled) disabled @endif
            />
            @if($clearable && ($search || $value))
                <button type="button" class="btn btn-ghost btn-square btn-xs" wire:click.stop="clear">
                    <x-icon name="o-x-mark" class="!h-4 text-error/80" />
                </button>
            @endif
            <x-icon name="o-chevron-up-down" class="!h-4" />
        </label>
    </fieldset>

    @if(true || $showDropdown)
        <ul class="shadow-md list bg-base-100 rounded-box">
            <li class="p-4 pb-2 text-xs tracking-wide opacity-60">Most played songs this week</li>
            @forelse($options as $opt)
                @php
                    $optLabel = data_get($opt, $optionLabel);
                    $optValue = data_get($opt, $optionValue);
                @endphp
                
                @if(!$multiple)
                <li class="">
                    @php
                        $isSelected = (string) $value !== '' && (string) $value === (string) $optValue;
                    @endphp
                    <label class="list-row cursor-pointer hover:bg-base-300/60 {{ $isSelected ? 'bg-base-300/60' : '' }}">
                        <input type="radio" name="{{ $id }}"
                            class="sr-only"
                            wire:model.live="value"
                            value="{{ $optValue }}"
                        />
                        <div class="grow">
                            <div class="truncate">{{ $optLabel }}</div>
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
                    <label class="list-row cursor-pointer hover:bg-base-300/60 {{ $isSelected ? 'bg-base-300/60' : '' }}">
                        <input type="checkbox"
                            name="{{ $id }}"
                            class="checkbox checkbox-sm"
                            wire:model.live="value"
                            value="{{ $optValue }}"
                        />
                        <div class="grow">
                            <div class="truncate">{{ $optLabel }}</div>
                        </div>
                    </label>
                </li>
                @endif
            @empty
                <li class="p-4 text-sm opacity-60">{{ $emptyText }}</li>
            @endforelse
        </ul>
    @endif
</div>