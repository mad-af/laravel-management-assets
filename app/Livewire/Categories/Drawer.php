<?php

namespace App\Livewire\Categories;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingCategoryId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'editCategory' => 'editCategory',
        'category-saved' => 'handleCategorySaved',
        'close-drawer' => 'closeDrawer'
    ];

    public function openDrawer()
    {
        $this->showDrawer = true;
        $this->editingCategoryId = null;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingCategoryId = null;
        $this->dispatch('resetForm');
    }

    public function editCategory($categoryId)
    {
        $this->editingCategoryId = $categoryId;
        $this->showDrawer = true;
    }

    public function handleCategorySaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.categories.drawer');
    }
}