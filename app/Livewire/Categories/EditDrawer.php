<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $category;
    public $categoryId;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeEditDrawer' => 'closeDrawer',
        'category-updated' => 'handleCategoryUpdated'
    ];

    public function openDrawer($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->category = Category::find($categoryId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->category = null;
        $this->categoryId = null;
        $this->dispatch('resetEditForm');
    }

    public function handleCategoryUpdated()
    {
        $this->closeDrawer();
        $this->dispatch('category-saved'); // Refresh table
    }

    public function render()
    {
        return view('livewire.categories.edit-drawer');
    }
}