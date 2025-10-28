<?php

namespace App\Livewire\Categories;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Enums\UserRole;

class Table extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = [
        'category-saved' => '$refresh',
        'category-updated' => '$refresh',
        'category-deleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openEditDrawer($categoryId)
    {
        $this->dispatch('open-edit-drawer', categoryId: $categoryId);
    }

    public function delete($categoryId)
    {
        try {
            Category::findOrFail($categoryId)->delete();
            $this->dispatch('category-deleted');
        } catch (\Throwable $e) {
            // handle error
        }
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.categories.table', compact('categories'));
    }

    public function getIsAdminProperty(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::ADMIN;
    }
}