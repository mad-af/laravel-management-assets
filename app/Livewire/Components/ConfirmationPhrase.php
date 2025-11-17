<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class ConfirmationPhrase extends Component
{
    public string $phrase = '';

    #[Modelable]
    public string $value = '';

    public function render()
    {
        return view('livewire.components.confirmation-phrase');
    }
}