<?php

namespace App\Livewire\Scanners;

use Livewire\Attributes\On;
use Livewire\Component;

class ScanResult extends Component
{
    public array $rows = [
            ['key' => 'Tag Scanned', 'value' => '-'],
            ['key' => 'Nama Aset', 'value' => '-'],
            ['key' => 'Kategori', 'value' => '-'],
            ['key' => 'Lokasi', 'value' => '-'],
            ['key' => 'Status', 'value' => '-'],
        ];
    public ?string $tagScanned = null;
    public ?array $assetScanned = null;

    public function mount() {
    }

    #[On('scanResult:updateAttributes')]
    public function updateAttributes($payload) {
        $this->tagScanned = $payload['tagScanned'];
        $this->assetScanned = $payload['assetScanned'];
        $this->updateRow();
    }

    public function updateRow() {
        $this->rows = [
            ['key' => 'Tag Scanned', 'value' => $this->tagScanned ?? '-'],
            ['key' => 'Nama Aset', 'value' => $this->assetScanned['name'] ?? '-'],
            ['key' => 'Kategori', 'value' => $this->assetScanned['category']['name'] ?? '-'],
            ['key' => 'Lokasi', 'value' => $this->assetScanned['location']['name'] ?? '-'],
            ['key' => 'Status', 'value' => $this->assetScanned['status'] ?? '-'],
        ];
    }

    public function render()
    {
        return view('livewire.scanners.scan-result');
    }   
}
