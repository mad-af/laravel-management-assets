<?php

namespace App\Livewire\Assets;

use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Exports\AssetActivityLogExport;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class QuickActionsCard extends Component
{
    use Toast;

    public Asset $asset;

    public bool $showConfirm = false;

    public string $confirmationPhrase = '';

    public string $value = '';

    public string $confirmAction = '';

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

            $this->success(
                "Asset '{$assetName}' berhasil dihapus.",
                'Asset Dihapus'
            );

            return redirect()->route('assets.index');
        } catch (\Exception $e) {
            $this->error(
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

    public function openDeactivateConfirm()
    {
        if (! $this->isAdmin) {
            $this->error('Aksi ini hanya untuk Admin.', 'Tidak diizinkan');

            return;
        }

        if ($this->asset->status === AssetStatus::INACTIVE) {
            $this->showInfoAlert('Asset sudah berstatus tidak aktif.');

            return;
        }

        $this->confirmAction = 'deactivate';
        $this->confirmationPhrase = 'NONAKTIFKAN ASSET '.$this->asset->code;
        $this->value = '';
        $this->showConfirm = true;
    }

    public function openActivateConfirm()
    {
        if (! $this->isAdmin) {
            $this->error('Aksi ini hanya untuk Admin.', 'Tidak diizinkan');

            return;
        }

        if ($this->asset->status === AssetStatus::ACTIVE) {
            $this->showInfoAlert('Asset sudah aktif.');

            return;
        }

        $this->confirmAction = 'activate';
        $this->confirmationPhrase = 'AKTIFKAN ASSET '.$this->asset->code;
        $this->value = '';
        $this->showConfirm = true;
    }

    public function confirmStatusChange()
    {
        if ($this->value !== $this->confirmationPhrase) {
            $this->error('Frasa konfirmasi tidak cocok. Mohon ketik tepat sama.', 'Konfirmasi gagal');

            return;
        }

        try {
            if ($this->confirmAction === 'deactivate') {
                $this->asset->status = AssetStatus::INACTIVE;
                $this->asset->save();
                $this->success("Status asset '{$this->asset->name}' diubah menjadi tidak aktif.", 'Berhasil');
            } elseif ($this->confirmAction === 'activate') {
                $this->asset->status = AssetStatus::ACTIVE;
                $this->asset->save();
                $this->success("Status asset '{$this->asset->name}' diubah menjadi aktif.", 'Berhasil');
            } else {
                $this->error('Aksi tidak dikenali.');
            }
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat memperbarui status asset.', 'Error');

            return;
        }

        // Tutup modal dan refresh model agar tombol berubah tanpa reload
        $this->showConfirm = false;
        $this->asset->refresh();
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

    public function getIsAdminProperty(): bool
    {
        $user = Auth::user();

        return $user && $user->role === UserRole::ADMIN;
    }
}
