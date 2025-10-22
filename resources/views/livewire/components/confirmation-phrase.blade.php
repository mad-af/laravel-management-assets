<fieldset class="p-4 w-full border fieldset bg-info/10 border-info/20 rounded-box">
    <legend class="fieldset-legend !text-info">Konfirmasi ulang</legend>

    {{-- Teks asli yang harus diketik ulang --}}
    <p class="mb-2 text-xs">
        <span class="block">Ketik frasa di bawah untuk menyimpan data.</span>
        <span class="block">Ketik: <span class="font-mono font-medium">"{{ $phrase }}"</span></span>
    </p>

    <label class="label">
        <span class="label-text">Ketik ulang frasa</span>
    </label>

    <input type="text" class="input input-sm input-info" wire:model.live="value" placeholder="{{ $phrase }}" />
    <p class="label">Harus sama persis, termasuk spasi & huruf besar/kecil.</p>
</fieldset>