<?php

namespace App\Livewire\Scanners;

use Livewire\Attributes\On;
use Livewire\Component;

class ScanResult extends Component
{
    // Scan Status Enum
    public const SCAN_STATUS_IDLE = 'idle';

    public const SCAN_STATUS_LOADING = 'loading';

    public const SCAN_STATUS_SUCCESS = 'success';

    public string $scanStatus = self::SCAN_STATUS_IDLE; // idle, loading, success

    public array $rows = [
        ['key' => 'Tag Scanned', 'value' => '-'],
        ['key' => 'Nama Aset', 'value' => '-'],
        ['key' => 'Kategori', 'value' => '-'],
        ['key' => 'Lokasi', 'value' => '-'],
        ['key' => 'Status', 'value' => '-'],
    ];

    public ?string $tagScanned = null;

    public ?array $assetScanned = null;

    public ?object $alert = null;

    public function mount() {}

    #[On('scanResult:updateAttributes')]
    public function updateAttributes($payload)
    {
        $this->scanStatus = $payload['scanStatus'] ?? $this->scanStatus;
        $this->tagScanned = $payload['tagScanned'] ?? null;
        $this->assetScanned = $payload['assetScanned'] ?? null;

        if ($this->scanStatus === self::SCAN_STATUS_SUCCESS) {
            $this->updateRow();
        }
    }

    public function updateRow()
    {
        $this->rows = [
            ['key' => 'Tag Scanned', 'value' => $this->tagScanned ?? '-'],
            ['key' => 'Nama Aset', 'value' => $this->assetScanned['name'] ?? '-'],
            ['key' => 'Kategori', 'value' => $this->assetScanned['category']['name'] ?? '-'],
            ['key' => 'Lokasi', 'value' => $this->assetScanned['location']['name'] ?? '-'],
            ['key' => 'Status', 'value' => $this->assetScanned['status'] ?? '-'],
        ];

        if ($this->assetScanned) {
            $this->alert = (object) [
                'type' => 'success',   // success, warning
                'title' => 'Aset Ditemukan',
                'message' => 'Kode aset berhasil ditemukan dalam sistem.',
            ];
        } else {
            $this->alert = (object) [
                'type' => 'warning', // success, warning
                'title' => 'Aset Tidak Ditemukan',
                'message' => 'Kode yang dipindai tidak terdaftar dalam sistem.',
            ];
        }
    }

    public function openDrawerMaintenance()
    {
        $this->dispatch('drawer:openDrawerMaintenance', assetId: $this->assetScanned['id']);
    }

    public function openDrawerCheckOut()
    {
        $this->dispatch('drawer:openDrawerCheckOut', assetId: $this->assetScanned['id']);
    }

    public function openDrawerCheckIn()
    {
        $this->dispatch('drawer:openDrawerCheckIn', assetId: $this->assetScanned['id']);
    }

    public function render()
    {
        return view('livewire.scanners.scan-result');
    }
}
