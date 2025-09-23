<x-info-card icon="o-viewfinder-circle" title="Scanner Kamera" class="flex-1">
  <div class="space-y-4">
    <div class="overflow-hidden relative rounded-lg bg-base-200" style="aspect-ratio: 4/3;">
      <video id="scanner-video" class="object-cover w-full h-full" autoplay muted playsinline></video>
      <div id="scanner-overlay" class="flex absolute inset-0 justify-center items-center">
        <div class="rounded-lg border-2 border-dashed border-primary size-56">
          <div class="flex justify-center items-center w-full h-full text-primary">
            <div class="space-y-6 text-center">
              <x-icon name="o-qr-code" class="mx-auto !size-18" />
              <p class="text-sm">Arahkan kamera ke QR/Barcode</p>
            </div>
          </div>
        </div>
      </div>
      <canvas id="scanner-canvas" class="hidden"></canvas>
    </div>

    <!-- Scanner Controls -->
    <div class="flex gap-4" x-data="{ isCameraActive: @entangle('isCameraActive') }">
      <x-button label="Mulai Scan" icon="o-play" class="flex-1 btn-primary btn-sm" wire:click="startScanner" />
      <x-button label="Stop Scan" icon="o-stop" class="flex-1 btn-sm" wire:click="stopScanner" x-bind:disabled="!isCameraActive" disabled />
      @if ($isSwitchCamera)
        <x-button icon="o-arrow-path" class="btn-sm" wire:click="switchCamera" />
      @endif
    </div>

    <!-- Scanner Status -->
    <div id="scanner-status" x-data="{ alert: @entangle('alert') }">
      <div class="alert alert-info" :class="{
        'alert-info': alert.type === 'info',
        'alert-success': alert.type === 'success',
        'alert-error': alert.type === 'error',
        'alert-warning': alert.type === 'warning',
      }" >
      <x-icon class="w-4 h-4" :name="match($alert->type) {
          'info' => 'o-information-circle',
          'success' => 'o-check-circle',
          'error' => 'o-x-circle',
          'warning' => 'o-exclamation-circle',
        }" />
        <div>
          <h3 class="font-bold">{{ $alert->title }}</h3>
          <div class="text-xs">{{ $alert->message }}</div>
        </div>
      </div>
    </div>
  </div>
</x-info-card>