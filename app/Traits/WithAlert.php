<?php

namespace App\Traits;

trait WithAlert
{
    /**
     * Show success alert
     */
    public function showSuccessAlert($message, $title = null)
    {
        $this->dispatch('showAlert', 'success', $message, $title);
    }

    /**
     * Show error alert
     */
    public function showErrorAlert($message, $title = null)
    {
        $this->dispatch('showAlert', 'error', $message, $title);
    }

    /**
     * Show warning alert
     */
    public function showWarningAlert($message, $title = null)
    {
        $this->dispatch('showAlert', 'warning', $message, $title);
    }

    /**
     * Show info alert
     */
    public function showInfoAlert($message, $title = null)
    {
        $this->dispatch('showAlert', 'info', $message, $title);
    }

    /**
     * Clear all alerts
     */
    public function clearAlerts()
    {
        $this->dispatch('clearAlerts');
    }
}