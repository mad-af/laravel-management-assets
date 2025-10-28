<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PasswordModal extends Component
{
    public bool $show = false;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected $listeners = [
        'open-password-modal' => 'openModal',
        'dismiss-password-modal' => 'closeModal',
    ];

    public function openModal(): void
    {
        $this->resetValidation();
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        if (! $user) {
            $this->addError('current_password', 'Anda harus login.');

            return;
        }

        if (! Hash::check($this->current_password, $user->getAuthPassword())) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');

            return;
        }

        $user->password = $this->password; // cast 'hashed' akan meng-hash otomatis
        $user->save();

        $this->show = false;
        $this->dispatch('password-updated');
    }

    public function render()
    {
        return view('livewire.profile.password-modal');
    }
}
