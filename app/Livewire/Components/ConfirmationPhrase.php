<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class ConfirmationPhrase extends Component
{
    #[Modelable]
    public string $value = '';

    public string $phrase = 'Data telah saya verifikasi';

    public function render()
    {
        return view('livewire.components.confirmation-phrase');
    }
}