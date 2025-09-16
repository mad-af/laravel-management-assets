<?php

namespace App\Livewire\Users;

use Livewire\Component;

class Drawer extends Component
{
    public $showDrawer = false;
    public $editingUserId = null;

    protected $listeners = [
        'openDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'user-saved' => 'closeDrawer',
    ];

    public function openDrawer($userId = null)
    {
        $this->editingUserId = $userId;
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingUserId = null;
        $this->dispatch('resetForm');
    }

    public function render()
    {
        return view('livewire.users.drawer');
    }
}