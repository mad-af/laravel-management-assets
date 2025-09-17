<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Component;

class ItemsTable extends Component
{
    public $itemsData;

    public function mount($itemsData)
    {
        $this->itemsData = $itemsData;
    }

    public function render()
    {
        return view('livewire.asset-transfers.items-table');
    }
}