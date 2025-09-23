<x-info-card icon="o-magnifying-glass" title="Hasil Scan" class="flex-1">

  <div class="space-y-4">
    @php
      $headers = [
        ['key' => 'key', 'label' => 'Key', 'class' => 'font-bold'],
        ['key' => 'value', 'label' => 'Value', 'class' => 'w-3/5'],
      ];
    @endphp
    <x-table :headers="$headers" :rows="$rows" no-headers no-hover />

    {{-- Alert Component --}}
    @if ($assetScanned)
    <div class="alert alert-success">
      <x-icon name="o-check-circle" class="w-4 h-4" />
      <div>
        <h3 class="font-bold">Aset Ditemukan</h3>
        <div class="text-xs">Kode aset berhasil ditemukan dalam sistem.</div>
      </div>
    </div>
    @elseif (!$assetScanned && $tagScanned) 
    <div class="alert alert-warning">
      <x-icon name="o-exclamation-triangle" class="w-4 h-4" />
      <div>
        <h3 class="font-bold">Aset Tidak Ditemukan</h3>
        <div class="text-xs">Kode yang dipindai tidak terdaftar dalam sistem.</div>
      </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="space-y-4">
      <div class="flex gap-4">
        <x-button label="Maintenance" icon="o-wrench-screwdriver" class="flex-1 w-full btn-secondary btn-sm" />
        <x-button label="Check Out" icon="o-arrow-up-tray" class="flex-1 w-full btn-accent btn-sm" />
      </div>

      <div class="">
        <x-button label="Check In" icon="o-arrow-down-tray" class="flex-1 w-full btn-primary btn-sm" />
      </div>

      <x-button label="Lihat Detail" icon="o-eye" class="w-full btn-sm" />
    </div>
    
  </div>

</x-info-card>