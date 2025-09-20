<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Url;

class Drawer extends Component
{
    #[Url(as: 'action')]       // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'user_id')]  // ?user_id=123
    public ?string $user_id = null;

    public bool $showDrawer = false;
    public ?string $editingUserId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
    ];

    public function mount()
    {
        $this->applyActionFromUrl();
    }

    public function updated($property)
    {
        if (in_array($property, ['action', 'user_id'])) {
            $this->applyActionFromUrl();
        }
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingUserId = null;
        } elseif ($this->action === 'edit' && $this->user_id) {
            $this->showDrawer = true;
            $this->editingUserId = $this->user_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($userId)
    {
        $this->action = 'edit';
        $this->user_id = $userId;
        $this->applyActionFromUrl();
    }

    public function openDrawer()
    {
        $this->action = 'create';
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingUserId = null;
        $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->user_id = null;
    }

    public function render()
    {
        return view('livewire.users.drawer');
    }
}