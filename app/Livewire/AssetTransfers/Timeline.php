<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Component;

class Timeline extends Component
{
    public $timelineData;

    public function mount($timelineData)
    {
        $this->timelineData = $timelineData;
    }

    public function render()
    {
        return view('livewire.asset-transfers.timeline');
    }
}