<?php

namespace App\Livewire\Users;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'user_id')] // ?user_id=123
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
        return $this->redirect(route('users.index', ['action' => 'edit', 'user_id' => $userId]), navigate: true);
    }

    public function openDrawer()
    {
        // Redirect dengan Livewire navigate (SPA), update URL query action=create
        return $this->redirect(route('users.index', ['action' => 'create']), navigate: true);
    }

    public function closeDrawer()
    {
        // Reset form lalu redirect SPA ke index untuk menghapus query param
        $this->dispatch('resetForm');

        return $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.users.drawer');
    }
}
