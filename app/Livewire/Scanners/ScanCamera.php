<?php

namespace App\Livewire\Scanners;

use Livewire\Attributes\On;
use Livewire\Component;

class ScanCamera extends Component
{
    // public bool $isSwitchCamera = false;
    public bool $isCameraActive = false;

    public bool $isSwitchCamera = false;

    public object $alert;

    public function mount()
    {
        $this->alert = (object) [
            'type' => 'info',   // info, success, error
            'title' => 'Aktifkan Kamera',
            'message' => 'Klik "Mulai Scan" untuk mengaktifkan kamera.',
        ];
    }

    #[On('scanner:updateAttributes')]
    public function updateAttributes($payload)
    {
        $this->isCameraActive = $payload['isCameraActive'] ?? $this->isCameraActive;
        $this->isSwitchCamera = $payload['isSwitchCamera'] ?? $this->isSwitchCamera;
        $this->alert = (object) $payload['alert'] ?? $this->alert;
    }

    // Tombol di Blade akan memanggil ini
    public function startScanner()
    {
        $this->dispatch('scanner:start');
    }

    public function stopScanner()
    {
        $this->dispatch('scanner:stop');
    }

    public function switchCamera()
    {
        $this->dispatch('scanner:switch');
    }

    public function render()
    {
        return view('livewire.scanners.scan-camera');
    }
}
