<?php

namespace App\Livewire\Assets;

use App\Enums\AssetStatus;
use App\Exports\AssetsExport;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Support\SessionKey;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast, WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $categoryFilter = '';

    public $branchFilter = '';

    public $selectedAssets = [];

    protected $queryString = ['search', 'statusFilter', 'categoryFilter', 'branchFilter'];

    protected $listeners = [
        'asset-saved' => '$refresh',
        'asset-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingBranchFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($assetId)
    {
        $this->dispatch('open-edit-drawer', assetId: $assetId);
    }

    public function deleteAsset($assetId)
    {
        try {
            $asset = Asset::findOrFail($assetId);
            $assetName = $asset->name;
            $asset->delete();

            $this->success("Asset '{$assetName}' berhasil dihapus.");

            $this->dispatch('asset-deleted');
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat menghapus asset.');
        }
    }

    #[On('print-qr-barcode')]
    public function printQRBarcode(?string $assetId = null)
    {
        // Jika ada assetId, print single asset
        if ($assetId) {
            $assets = collect([Asset::findOrFail($assetId)]);
        }
        // Jika tidak ada assetId, gunakan selectedAssets untuk batch print
        elseif (! empty($this->selectedAssets)) {
            $assets = Asset::whereIn('id', $this->selectedAssets)->get();
        }
        // Jika tidak ada yang dipilih
        else {
            $this->info(
                'Pilih aset yang akan dicetak dulu.',
            );

            return;
        }

        // Generate HTML dengan semua assets
        $html = view('pdf-template.lebel-asset', compact('assets'))->render();

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

    #[On('download-asset')]
    public function downloadAsset()
    {
        // Get current branch ID from session
        $currentBranchId = session_get(SessionKey::BranchId);

        if (! $currentBranchId) {
            $this->error('Branch ID tidak ditemukan dalam session.');

            return;
        }

        // Get branch name for filename
        $branch = Branch::find($currentBranchId);
        $branchName = $branch ? str_replace(' ', '_', $branch->name) : 'Unknown_Branch';

        $filename = 'Assets_'.$branchName.'_'.now()->format('Y-m-d_H-i-s').'.xlsx';

        try {
            return Excel::download(new AssetsExport($currentBranchId), $filename);
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan saat mengunduh file Excel: '.$e->getMessage());
        }
    }

    public function render()
    {
        // Get current branch ID from session
        $currentBranchId = session_get(SessionKey::BranchId);

        $assets = Asset::query()
            ->with(['category', 'branch'])
            ->when($currentBranchId, function ($query) use ($currentBranchId) {
                $query->where('branch_id', $currentBranchId);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhere('tag_code', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->branchFilter, function ($query) {
                $query->where('branch_id', $this->branchFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::active()->orderBy('name')->get();
        $statuses = AssetStatus::cases();
        // dd($assets);

        return view('livewire.assets.table', compact('assets', 'categories', 'statuses'));
    }
}
