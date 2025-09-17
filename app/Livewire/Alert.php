<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Alert extends Component
{
    public array $alerts = [];
    public bool $autoHide = true;
    public int $hideDelay = 5000; // 5 seconds

    #[On('showAlert')]
    public function addAlert(string $type, string $message, ?string $title = null): void
    {
        $alertId = uniqid();
        
        $this->alerts[] = [
            'id' => $alertId,
            'type' => $type, // success, error, warning, info
            'title' => $title,
            'message' => $message,
            'timestamp' => now()->timestamp
        ];

        // Auto hide after delay if enabled
        if ($this->autoHide) {
            $this->dispatch('auto-hide-alert', id: $alertId, delay: $this->hideDelay);
        }
    }

    #[On('hideAlert')]
    public function removeAlert(string $alertId): void
    {
        $this->alerts = array_filter($this->alerts, fn($alert) => $alert['id'] !== $alertId);
    }

    #[On('clearAlerts')]
    public function clearAllAlerts(): void
    {
        $this->alerts = [];
    }

    public function getAlertIcon(string $type): string
    {
        return match($type) {
            'success' => 'o-check-circle',
            'error' => 'o-x-circle',
            'warning' => 'o-exclamation-triangle',
            'info' => 'o-information-circle',
            default => 'o-information-circle'
        };
    }

    public function getAlertClass(string $type): string
    {
        return match($type) {
            'success' => 'alert-success',
            'error' => 'alert-error',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-info'
        };
    }

    public function render()
    {
        return view('livewire.alert');
    }
}