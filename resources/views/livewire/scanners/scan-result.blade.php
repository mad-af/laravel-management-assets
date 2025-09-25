<x-info-card icon="o-magnifying-glass" title="Hasil Scan" class="flex-1">

  <div class="space-y-4">
    
    {{-- Success State --}}
    <div class="space-y-4 {{ $scanStatus === self::SCAN_STATUS_SUCCESS ? 'block' : 'hidden' }}">
      @php
        $headers = [
          ['key' => 'key', 'label' => 'Key', 'class' => 'font-bold'],
          ['key' => 'value', 'label' => 'Value', 'class' => 'w-3/5'],
        ];
      @endphp
      <x-table :headers="$headers" :rows="$rows" no-headers no-hover />
      
      {{-- Alert Component --}}
      <div class="alert-container">
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
      </div>

      {{-- Action Buttons --}}
      <div class="action-buttons {{ $assetScanned ? 'block' : 'hidden' }}">
        <div class="space-y-4">
          <div class="status-buttons {{ isset($assetScanned['status']) && $assetScanned['status'] === \App\Enums\AssetStatus::ACTIVE->value ? 'block' : 'hidden' }}">
            <div class="flex gap-4">
              <x-button label="Maintenance" icon="o-wrench-screwdriver" class="flex-1 w-full btn-secondary btn-sm" />
              <x-button label="Check Out" icon="o-arrow-up-tray" class="flex-1 w-full btn-accent btn-sm" />
            </div>
          </div>
          
          <div class="checkin-button {{ isset($assetScanned['status']) && $assetScanned['status'] === \App\Enums\AssetStatus::CHECKED_OUT->value ? 'block' : 'hidden' }}">
            <x-button label="Check In" icon="o-arrow-down-tray" class="flex-1 w-full btn-primary btn-sm" />
          </div>

          <x-button label="Lihat Detail" icon="o-eye" class="w-full btn-sm" />
        </div>
      </div>
    </div>
    
    {{-- Loading State --}}
    <div class="{{ $scanStatus === self::SCAN_STATUS_LOADING ? 'block' : 'hidden' }}">
      <div class="w-full h-56 skeleton"></div>
    </div>
    
    {{-- Idle State --}}
    <div class="space-y-4 {{ $scanStatus === self::SCAN_STATUS_IDLE ? 'block' : 'hidden' }}">
      <div class="text-center">
        <div class="mb-4">
          <x-icon name="o-viewfinder-circle" class="mx-auto w-16 h-16 text-base-content/30" />
        </div>
        <h3 class="mb-2 text-lg font-semibold text-base-content">Belum Ada Scan</h3>
        <p class="mb-4 text-sm text-base-content/70">
          Silakan lakukan scan QR code atau barcode terlebih dahulu untuk melihat informasi aset.
        </p>
        
        <div class="p-4 text-left rounded-lg border bg-info/10 border-info/20">
          <div class="flex items-start">
            <x-icon name="o-information-circle" class="flex-shrink-0 mt-0.5 mr-3 w-5 h-5 text-info" />
            <div class="text-sm text-info">
              <p class="mb-1 font-medium">Cara melakukan scan:</p>
              <ol class="space-y-1 text-xs list-decimal list-inside">
                <li>Klik tombol "Mulai Scan" pada kamera</li>
                <li>Arahkan kamera ke QR code atau barcode aset</li>
                <li>Tunggu hingga kode berhasil terbaca</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</x-info-card>