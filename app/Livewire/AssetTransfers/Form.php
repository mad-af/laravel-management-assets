<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Models\AssetTransferItem;
use App\Models\Location;
use App\Models\Asset;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use Toast;

    public $transferId;
    public $transfer_no = '';
    public $reason = '';
    public $status = 'draft';
    public $from_location_id = '';
    public $to_location_id = '';
    public $scheduled_at = '';
    public $isEdit = false;
    public $items = [];

    protected $rules = [
        'transfer_no' => 'required|string|max:255',
        'reason' => 'nullable|string',
        'status' => 'required|in:draft,submitted,approved,executed,void',
        'from_location_id' => 'nullable|exists:locations,id',
        'to_location_id' => 'nullable|exists:locations,id',
        'scheduled_at' => 'nullable|date',
        'items' => 'required|array|min:1',
        'items.*.asset_id' => 'required|exists:assets,id',
        'items.*.from_location_id' => 'nullable|exists:locations,id',
        'items.*.to_location_id' => 'nullable|exists:locations,id',
        'items.*.status' => 'required|in:pending,in_transit,delivered,failed,cancelled',
        'items.*.notes' => 'nullable|string',
    ];

    protected $listeners = [
        'editTransfer' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($transferId = null)
    {
        $this->transferId = $transferId;
        
        if ($transferId) {
            $this->isEdit = true;
            $this->loadTransfer();
        } else {
            $this->generateTransferNo();
            $this->addItem(); // Add initial item
        }
    }

    public function loadTransfer()
    {
        if ($this->transferId) {
            $transfer = AssetTransfer::with('items')->find($this->transferId);
            if ($transfer) {
                $this->transfer_no = $transfer->transfer_no;
                $this->reason = $transfer->reason;
                $this->status = $transfer->status;
                $this->from_location_id = $transfer->from_location_id;
                $this->to_location_id = $transfer->to_location_id;
                $this->scheduled_at = $transfer->scheduled_at ? $transfer->scheduled_at->format('Y-m-d\\TH:i') : '';
                
                // Load items
                $this->items = $transfer->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'asset_id' => $item->asset_id,
                        'from_location_id' => $item->from_location_id,
                        'to_location_id' => $item->to_location_id,
                        'status' => $item->status,
                        'notes' => $item->notes,
                    ];
                })->toArray();
            }
        }
    }

    public function generateTransferNo()
    {
        $this->transfer_no = 'TRF-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function addItem()
    {
        $this->items[] = [
            'asset_id' => '',
            'from_location_id' => $this->from_location_id,
            'to_location_id' => $this->to_location_id,
            'status' => 'pending',
            'notes' => '',
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Re-index array
        }
    }

    public function updatedFromLocationId($value)
    {
        // Update all items' from_location_id when main from_location changes
        foreach ($this->items as $index => $item) {
            $this->items[$index]['from_location_id'] = $value;
        }
    }

    public function updatedToLocationId($value)
    {
        // Update all items' to_location_id when main to_location changes
        foreach ($this->items as $index => $item) {
            $this->items[$index]['to_location_id'] = $value;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'transfer_no' => $this->transfer_no,
                'reason' => $this->reason,
                'status' => $this->status,
                'from_location_id' => $this->from_location_id ?: null,
                'to_location_id' => $this->to_location_id ?: null,
                'scheduled_at' => $this->scheduled_at ? date('Y-m-d H:i:s', strtotime($this->scheduled_at)) : null,
            ];

            if ($this->isEdit && $this->transferId) {
                $transfer = AssetTransfer::find($this->transferId);
                $transfer->update($data);
                
                // Update items
                $transfer->items()->delete(); // Delete existing items
                foreach ($this->items as $itemData) {
                    $transfer->items()->create([
                        'asset_id' => $itemData['asset_id'],
                        'from_location_id' => $itemData['from_location_id'],
                        'to_location_id' => $itemData['to_location_id'],
                        'status' => $itemData['status'],
                        'notes' => $itemData['notes'],
                    ]);
                }
                
                $this->success('Asset Transfer updated successfully!');
                $this->dispatch('transfer-updated');
            } else {
                $data['requested_by'] = Auth::id();
                $transfer = AssetTransfer::create($data);
                
                // Create items
                foreach ($this->items as $itemData) {
                    $transfer->items()->create([
                        'asset_id' => $itemData['asset_id'],
                        'from_location_id' => $itemData['from_location_id'],
                        'to_location_id' => $itemData['to_location_id'],
                        'status' => $itemData['status'],
                        'notes' => $itemData['notes'],
                    ]);
                }
                
                $this->success('Asset Transfer created successfully!');
                $this->dispatch('transfer-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->transfer_no = '';
        $this->reason = '';
        $this->status = 'draft';
        $this->from_location_id = '';
        $this->to_location_id = '';
        $this->scheduled_at = '';
        $this->items = [];
        $this->resetValidation();
        $this->generateTransferNo();
        $this->addItem(); // Add initial item
    }

    public function render()
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $assets = Asset::where('status', 'active')->orderBy('name')->get();
        
        return view('livewire.asset-transfers.form', [
            'locations' => $locations,
            'assets' => $assets
        ]);
    }
}