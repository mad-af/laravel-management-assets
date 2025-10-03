<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class VehicleProfileAlert extends Component
{
    public bool $showAlert = false;
    public string $assetId = '';
    public string $assetName = '';
    public string $profileUrl = '';

    #[On('show-vehicle-profile-alert')]
    public function showVehicleProfileAlert(array $data): void
    {
        $this->assetId = $data['assetId'] ?? '';
        $this->assetName = $data['assetName'] ?? '';
        $this->profileUrl = $data['profileUrl'] ?? '';
        $this->showAlert = true;
    }

    public function proceedToProfile(): void
    {
        $this->showAlert = false;
        // Redirect to vehicle profile page
        $this->redirect($this->profileUrl);
    }

    public function cancelAlert(): void
    {
        $this->showAlert = false;
        // Reset data
        $this->reset(['assetId', 'assetName', 'profileUrl']);
    }

    public function render()
    {
        return view('livewire.vehicle-profile-alert');
    }
}