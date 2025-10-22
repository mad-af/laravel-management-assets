<?php

namespace App\Livewire\Scanners;

use Livewire\Attributes\On;
use Livewire\Component;

class ScanCamera extends Component
{
    public string $cameraStatus = 'off'; // on, off, preparing

    public bool $isSwitchCamera = false;

    public ?object $alert = null;

    public function mount()
    {
        $this->alert = (object) [
            'type' => 'info',   // info, success, error, warning
            'title' => 'Aktifkan Kamera',
            'message' => 'Klik "Mulai Scan" untuk mengaktifkan kamera.',
        ];
    }

    #[On('scanCamera:updateAttributes')]
    public function updateAttributes($payload)
    {
        $this->cameraStatus = $payload['cameraStatus'] ?? $this->cameraStatus;
        $this->isSwitchCamera = $payload['isSwitchCamera'] ?? $this->isSwitchCamera;
        $this->alert = (object) $payload['alert'] ?? $this->alert;

        if ($this->cameraStatus === 'off') {
            $this->isSwitchCamera = false;
        }
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
