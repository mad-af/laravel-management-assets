<?php

namespace App\Livewire\Assets;

use App\Exports\AssetActivityLogExport;
use App\Models\Asset;
use App\Traits\WithAlert;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

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

    public function downloadActivityLog()
    {
        try {
            // Generate filename with asset name and timestamp
            $assetName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $this->asset->name);
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "Activity_Log_{$assetName}_{$timestamp}.xlsx";

            // Download the Excel file using the AssetActivityLogExport class
            return Excel::download(new AssetActivityLogExport($this->asset), $filename);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error downloading activity log: '.$e->getMessage());

            // Show error message to user
            $this->alert('error', 'Gagal mengunduh activity log. Silakan coba lagi.');

            return null;
        }
    }

    public function render()
    {
        return view('livewire.assets.quick-actions-card');
    }
}
