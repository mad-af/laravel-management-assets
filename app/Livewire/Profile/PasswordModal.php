<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class PasswordModal extends Component
{
    public bool $passwordModal = false;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[On('open-password-modal')]
    public function openModal(): void
    {
        $this->resetValidation();
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->passwordModal = true;
    }

    #[On('dismiss-password-modal')]
    public function closeModal(): void
    {
        $this->passwordModal = false;
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

        // Tutup modal, logout, dan redirect ke halaman login
        $this->passwordModal = false;
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        // Optional: dispatch event sebelum redirect jika perlu
        // $this->dispatch('password-updated');

        redirect('/login');
    }

    public function render()
    {
        return view('livewire.profile.password-modal');
    }
}
