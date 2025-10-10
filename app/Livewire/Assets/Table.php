<?php

namespace App\Livewire\Assets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithAlert, WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $categoryFilter = '';

    public $branchFilter = '';

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

            $this->showSuccessAlert(
                "Asset '{$assetName}' berhasil dihapus.",
                'Asset Dihapus'
            );

            $this->dispatch('asset-deleted');
        } catch (\Exception $e) {
            $this->showErrorAlert(
                'Terjadi kesalahan saat menghapus asset.',
                'Error'
            );
        }
    }

    public function printQRBarcode($assetId)
    {
        $asset = Asset::findOrFail($assetId);

        $url = route('qr.gateway', ['tag_code' => $asset->tag_code]);
        $html = view('pdf-template.lebel-asset', compact('asset'))->render();

        $this->dispatch('print-qrbarcode', tagCode: $asset->tag_code, url: $url, html: $html);
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
