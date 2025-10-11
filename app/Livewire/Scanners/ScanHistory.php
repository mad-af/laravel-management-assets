<?php

namespace App\Livewire\Scanners;

use App\Models\Asset;
use Livewire\Attributes\On;
use Livewire\Component;

class ScanHistory extends Component
{
    public array $rows = [];

    public function mount()
    {
        $this->dispatch('scanner:history');
    }

    #[On('scanHistory:updateAttributes')]
    public function updateAttributes($payload)
    {
        $this->rows = $payload['rows'] ?? $this->rows;
    }

    public function getAssetAttribute($id)
    {
        return Asset::find($id);
    }

    public function render()
    {
        return view('livewire.scanners.scan-history');
    }
}
