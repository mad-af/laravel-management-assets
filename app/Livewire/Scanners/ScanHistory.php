<?php

namespace App\Livewire\Scanners;

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
        // dd('abc', $payload['rows']);
        $this->rows = $payload['rows'] ?? $this->rows;
    }

    public function render()
    {
        return view('livewire.scanners.scan-history');
    }
}
