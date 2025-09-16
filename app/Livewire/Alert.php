<?php

namespace App\Livewire;

use Livewire\Component;

class Alert extends Component
{
    public $alerts = [];
    public $autoHide = true;
    public $hideDelay = 5000; // 5 seconds

    protected $listeners = [
        'showAlert' => 'addAlert',
        'hideAlert' => 'removeAlert',
        'clearAlerts' => 'clearAllAlerts'
    ];

    public function mount($autoHide = true, $hideDelay = 5000)
    {
        $this->autoHide = $autoHide;
        $this->hideDelay = $hideDelay;
    }

    public function addAlert($type, $message, $title = null)
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
            $this->dispatch('auto-hide-alert', ['id' => $alertId, 'delay' => $this->hideDelay]);
        }
    }

    public function removeAlert($alertId)
    {
        $this->alerts = array_filter($this->alerts, function($alert) use ($alertId) {
            return $alert['id'] !== $alertId;
        });
    }

    public function clearAllAlerts()
    {
        $this->alerts = [];
    }

    public function getAlertIcon($type)
    {
        return match($type) {
            'success' => 'check-circle',
            'error' => 'x-circle',
            'warning' => 'alert-triangle',
            'info' => 'info',
            default => 'info'
        };
    }

    public function getAlertClass($type)
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