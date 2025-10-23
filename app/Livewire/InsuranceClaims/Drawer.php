<?php

namespace App\Livewire\InsuranceClaims;

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
        $this->action = 'edit';
        $this->category_id = $categoryId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($categoryId = null)
    {
        if ($categoryId) {
            $this->action = 'edit';
            $this->category_id = $categoryId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingCategoryId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->category_id = null;
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