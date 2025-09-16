<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Enums\AssetStatus;
use App\Traits\WithAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination, WithAlert;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $locationFilter = '';

    protected $queryString = ['search', 'statusFilter', 'categoryFilter', 'locationFilter'];

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

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('openDrawer');
    }

    public function openEditDrawer($assetId)
    {
        $this->dispatch('openEditDrawer', $assetId);
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

    public function render()
    {
        $assets = Asset::query()
            ->with(['category', 'location'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('tag_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->locationFilter, function ($query) {
                $query->where('location_id', $this->locationFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('name')->get();
        $statuses = AssetStatus::cases();

        return view('livewire.assets.table', compact('assets', 'categories', 'locations', 'statuses'));
    }
}