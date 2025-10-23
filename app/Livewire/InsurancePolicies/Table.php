<?php

namespace App\Livewire\InsurancePolicies;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    protected $listeners = [
        'category-saved' => '$refresh',
        'category-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openDrawer()
    {
        $this->dispatch('open-drawer');
    }

    public function openEditDrawer($categoryId)
    {
        $this->dispatch('open-edit-drawer', categoryId: $categoryId);
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.categories.table', compact('categories'));
    }
}