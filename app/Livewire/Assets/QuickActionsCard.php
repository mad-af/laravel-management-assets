<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Traits\WithAlert;
use Livewire\Component;

class QuickActionsCard extends Component
{
    use WithAlert;

    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function editAsset()
    {
        $this->dispatch('open-edit-drawer', assetId: $this->asset->id);
    }

    public function deleteAsset()
    {
        try {
            $assetName = $this->asset->name;
            $this->asset->delete();

            $this->showSuccessAlert(
                "Asset '{$assetName}' berhasil dihapus.",
                'Asset Dihapus'
            );

            return redirect()->route('assets.index');
        } catch (\Exception $e) {
            $this->showErrorAlert(
                'Terjadi kesalahan saat menghapus asset.',
                'Error'
            );
        }
    }

    public function printQRCode()
    {
        $this->dispatch('print-qr-barcode', assetId: $this->asset->id);
    }

    public function viewAssetLogs()
    {
        // Navigate to asset logs or open modal
        return redirect()->route('asset-logs.index', ['asset_id' => $this->asset->id]);
    }

    public function printQRBarcode()
    {
        // Jika ada assetId, print single asset
        $assets = collect([$this->asset]);

        // Generate HTML dengan semua assets
        $html = view('pdf-template.lebel-asset', data: compact('assets'))->render();

        // Prepare data untuk JavaScript
        $assetsData = $assets->map(function ($asset) {

            return [
                'id' => $asset->id,
                'tag_code' => $asset->tag_code,
                'url' => route('qr.gateway', parameters: ['tag_code' => $asset->tag_code]),
            ];
        })->toArray();

        $this->dispatch('print-qrbarcode', assets: $assetsData, html: $html);
    }

    public function render()
    {
        return view('livewire.assets.quick-actions-card');
    }
}
