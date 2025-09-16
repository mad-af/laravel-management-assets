<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class EditDrawer extends Component
{
    public $showDrawer = false;
    public $user;

    protected $listeners = [
        'openEditDrawer' => 'openDrawer',
        'closeDrawer' => 'closeDrawer',
        'user-updated' => 'closeDrawer',
    ];

    public function openDrawer($userId)
    {
        $this->user = User::with(['company'])->find($userId);
        $this->showDrawer = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->user = null;
    }

    public function render()
    {
        return view('livewire.users.edit-drawer');
    }
}