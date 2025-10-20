<?php

namespace App\Livewire\AssetTransfers;

use App\Enums\AssetTransferStatus;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Branch;
use App\Traits\WithAlert;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast, WithAlert;

    public $transferId;

    public $transfer_no = '';

    public $reason = '';

    public $from_location_id = '';

    public $to_location_id = '';

    public $status = 'draft';

    public $priority = 'medium';

    public $scheduled_at = '';

    public $notes = '';

    public $isEdit = false;

    // Asset items
    public $items = [];

    protected $rules = [
        'reason' => 'required|string|max:500',
        'from_location_id' => 'required|exists:branches,id',
        'to_location_id' => 'required|exists:branches,id|different:from_location_id',
        'status' => 'required|string',
        'priority' => 'required|string',
        'scheduled_at' => 'nullable|date',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.asset_id' => 'required|exists:assets,id',
        'items.*.from_location_id' => 'nullable|exists:branches,id',
        'items.*.to_location_id' => 'nullable|exists:branches,id',
    ];

    protected $listeners = [
        'editTransfer' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function updated($propertyName)
    {
        if ($propertyName === 'from_location_id') {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['from_location_id'] = $this->from_location_id;
            }
        }

        if ($propertyName === 'to_location_id') {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['to_location_id'] = $this->to_location_id;
            }
        }
    }

    public function mount($transferId = null)
    {
        $this->transferId = $transferId;

        if ($transferId) {
            $this->isEdit = true;
            $this->loadTransfer();
        } else {
            // Add default item
            $this->addItem();
        }
    }

    public function loadTransfer()
    {
        if ($this->transferId) {
            $transfer = AssetTransfer::with('items')->find($this->transferId);
            if ($transfer) {
                $this->transfer_no = $transfer->transfer_no;
                $this->reason = $transfer->reason;
                $this->from_location_id = $transfer->from_location_id;
                $this->to_location_id = $transfer->to_location_id;
                $this->status = $transfer->status->value;
                $this->priority = $transfer->priority->value;
                $this->scheduled_at = $transfer->scheduled_at?->format('Y-m-d\\TH:i');
                $this->notes = $transfer->notes;

                $this->items = $transfer->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'asset_id' => $item->asset_id,
                        'notes' => $item->notes,
                        'from_location_id' => $this->from_location_id,
                        'to_location_id' => $this->to_location_id,
                    ];
                })->toArray();
            }
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'asset_id' => '',
            'from_location_id' => $this->from_location_id,
            'to_location_id' => $this->to_location_id,
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function resetForm()
    {
        $this->transfer_no = '';
        $this->reason = '';
        $this->from_location_id = '';
        $this->to_location_id = '';
        $this->status = 'draft';
        $this->priority = 'medium';
        $this->scheduled_at = '';
        $this->notes = '';
        $this->items = [];
        $this->resetValidation();

        if (! $this->isEdit) {
            $this->addItem();
        }
    }

    public function render()
    {
        $branches = Branch::all();
        $assets = Asset::where('company_id', Auth::user()?->company_id)->get()->map(function ($asset) {
            $asset->display_name = $asset->name.' ('.$asset->asset_tag.')';

            return $asset;
        });

        $statusOptions = collect(AssetTransferStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        });

      

        return view('livewire.asset-transfers.form', compact('branches', 'assets', 'statusOptions'))
            ->with('transferId', $this->transferId)
            ->with('isEdit', $this->isEdit);
    }
}
