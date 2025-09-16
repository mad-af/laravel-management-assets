<?php

namespace App\Livewire\Scanners;

use App\Models\Asset;
use App\Traits\WithAlert;
use Livewire\Component;

class ScannerInterface extends Component
{
    use WithAlert;

    public $scannedCode = '';
    public $asset = null;
    public $isScanning = false;
    public $scanHistory = [];

    protected $listeners = [
        'codeScanned' => 'handleScannedCode',
        'scannerStarted' => 'setScanningState',
        'scannerStopped' => 'setScanningState',
    ];

    public function mount()
    {
        $this->loadRecentScans();
    }

    public function handleScannedCode($code)
    {
        $this->scannedCode = $code;
        $this->searchAsset($code);
        $this->addToScanHistory($code);
    }

    public function searchAsset($code)
    {
        // Search by tag_code first, then by code
        $this->asset = Asset::with(['category', 'location'])
            ->where('tag_code', $code)
            ->orWhere('code', $code)
            ->first();

        if ($this->asset) {
            $this->showSuccessAlert(
                "Asset '{$this->asset->name}' ditemukan!",
                'Scan Berhasil'
            );
        } else {
            $this->showWarningAlert(
                'Kode yang dipindai tidak terdaftar dalam sistem.',
                'Asset Tidak Ditemukan'
            );
        }
    }

    public function setScanningState($isScanning = false)
    {
        $this->isScanning = $isScanning;
    }

    public function clearResult()
    {
        $this->scannedCode = '';
        $this->asset = null;
    }

    public function openCheckoutDrawer()
    {
        if ($this->asset) {
            $this->dispatch('openCheckoutDrawer', $this->asset->id);
        }
    }

    public function openCheckinDrawer()
    {
        if ($this->asset) {
            $this->dispatch('openCheckinDrawer', $this->asset->id);
        }
    }

    public function updateAssetStatus($status)
    {
        if ($this->asset) {
            try {
                $this->asset->update(['status' => $status]);
                $this->asset->refresh();
                
                $this->showSuccessAlert(
                    "Status asset berhasil diubah ke {$status}.",
                    'Status Diperbarui'
                );
            } catch (\Exception $e) {
                $this->showErrorAlert(
                    'Terjadi kesalahan saat mengubah status asset.',
                    'Error'
                );
            }
        }
    }

    public function viewAssetDetail()
    {
        if ($this->asset) {
            return redirect()->route('admin.assets.show', $this->asset->id);
        }
    }

    private function addToScanHistory($code)
    {
        $scanData = [
            'code' => $code,
            'asset' => $this->asset,
            'scanned_at' => now(),
        ];

        // Add to beginning of array and limit to 10 items
        array_unshift($this->scanHistory, $scanData);
        $this->scanHistory = array_slice($this->scanHistory, 0, 10);
    }

    private function loadRecentScans()
    {
        // Load recent scans from session or database if needed
        $this->scanHistory = session('recent_scans', []);
    }

    public function render()
    {
        return view('livewire.scanners.scanner-interface');
    }
}