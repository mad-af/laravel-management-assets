<div>
    <x-select :label="$label ?? 'Bulan dan tanggal'" wire:model.live="month" :options="$monthOptions" option-value="value"
        option-label="label" inline class="select-sm" placeholder="Pilih Bulan" required>
        <x-slot:append>
                <select class="select select-sm !border-base-content/15 rounded-r" wire:model.live="day"
                    placeholder="Pilih Tanggal" {{ $month ? '' : 'disabled' }} required>
                    <option value="">Pilih Tanggal</option>
                    @foreach($dayOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
        </x-slot:append>
    </x-select>



</div>