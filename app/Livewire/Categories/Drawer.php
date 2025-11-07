<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'category_id')]  // ?category_id=123
    public ?string $category_id = null;

    public bool $showDrawer = false;
    public ?string $editingCategoryId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'category-saved' => 'closeDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl(); // hanya sekali di initial load
    }

    // Dipanggil kalau kamu ubah action via property (akan auto update URL)
    public function updatedAction($value)
    {
        $this->applyActionFromUrl();
    }

    public function updatedCategoryId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingCategoryId = null;
        } elseif ($this->action === 'edit' && $this->category_id) {
            $this->showDrawer   = true;
            $this->editingCategoryId = $this->category_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($categoryId)
    {
        $this->redirect(route('categories.index', [
            'action' => 'edit',
            'category_id' => $categoryId,
        ]), navigate: true);
    }

    public function openDrawer($categoryId = null)
    {
        if ($categoryId) {
            $this->redirect(route('categories.index', [
                'action' => 'edit',
                'category_id' => $categoryId,
            ]), navigate: true);
        } else {
            $this->redirect(route('categories.index', [
                'action' => 'create',
            ]), navigate: true);
        }
    }

    public function closeDrawer()
    {
        $this->dispatch('resetForm');
        $this->redirect(route('categories.index'), navigate: true);
    }

    public function editCategory($categoryId)
    {
        $this->openEditDrawer($categoryId);
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