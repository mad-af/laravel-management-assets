<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use App\Models\AssetTransferItem;
use App\Models\Location;
use App\Models\Asset;
use App\Models\User;
use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferItemStatus;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use Toast;

    public $transferId;
    public $transfer_no = '';
    public $reason = '';
    public $from_location_id = '';
    public $to_location_id = '';
    public $status = 'draft';
    public $scheduled_at = '';
    public $notes = '';
    public $isEdit = false;
    
    // Asset items
    public $items = [];

    protected $rules = [
        'transfer_no' => 'required|string|max:255',
        'reason' => 'required|string|max:500',
        'from_location_id' => 'required|exists:locations,id',
        'to_location_id' => 'required|exists:locations,id|different:from_location_id',
        'status' => 'required|string',
        'scheduled_at' => 'nullable|date',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.asset_id' => 'required|exists:assets,id',
        'items.*.from_location_id' => 'nullable|exists:locations,id',
        'items.*.to_location_id' => 'nullable|exists:locations,id'
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
        }
    }

    public function generateTransferNo()
    {
        $this->transfer_no = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4));
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
                $this->scheduled_at = $transfer->scheduled_at?->format('Y-m-d\\TH:i');
                $this->notes = $transfer->notes;
                
                $this->items = $transfer->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'asset_id' => $item->asset_id,
                        'notes' => $item->notes
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
            'to_location_id' => $this->to_location_id
        ];
    }

    public function updatedFromLocationId($value)
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['from_location_id'] = $value;
        }
    }

    public function updatedToLocationId($value)
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['to_location_id'] = $value;
        }
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->items)) {
            $this->error('Minimal 1 item aset harus ditambahkan.');
            return;
        }

        try {
            if ($this->isEdit && $this->transferId) {
                $transfer = AssetTransfer::find($this->transferId);
                $transfer->update([
                    'transfer_no' => $this->transfer_no,
                    'reason' => $this->reason,
                    'from_location_id' => $this->from_location_id,
                    'to_location_id' => $this->to_location_id,
                    'status' => AssetTransferStatus::from($this->status),
                    'scheduled_at' => $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null,
                    'notes' => $this->notes,
                    'requested_by' => Auth::id(),
                ]);
                
                // Update items
                $transfer->items()->delete();
                foreach ($this->items as $item) {
                    AssetTransferItem::create([
                        'asset_transfer_id' => $transfer->id,
                        'asset_id' => $item['asset_id'],
                        'from_location_id' => $this->from_location_id,
                        'to_location_id' => $this->to_location_id,
                        'notes' => $item['notes'] ?? '',
                        'status' => AssetTransferItemStatus::PENDING,
                    ]);
                }
                
                $this->success('Asset Transfer updated successfully!');
                $this->dispatch('transfer-updated');
            } else {
                $transfer = AssetTransfer::create([
                    'transfer_no' => $this->transfer_no,
                    'reason' => $this->reason,
                    'from_location_id' => $this->from_location_id,
                    'to_location_id' => $this->to_location_id,
                    'status' => AssetTransferStatus::from($this->status),
                    'scheduled_at' => $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null,
                    'notes' => $this->notes,
                    'requested_by' => Auth::id(),
                    'company_id' => Auth::user()?->company_id,
                ]);
                
                // Create items
                foreach ($this->items as $item) {
                    AssetTransferItem::create([
                        'asset_transfer_id' => $transfer->id,
                        'asset_id' => $item['asset_id'],
                        'from_location_id' => $this->from_location_id,
                        'to_location_id' => $this->to_location_id,
                        'notes' => $item['notes'] ?? '',
                        'status' => AssetTransferItemStatus::PENDING,
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
        $this->from_location_id = '';
        $this->to_location_id = '';
        $this->status = 'draft';
        $this->scheduled_at = '';
        $this->notes = '';
        $this->items = [];
        $this->resetValidation();
        
        if (!$this->isEdit) {
            $this->generateTransferNo();
        }
    }

    public function render()
    {
        $locations = Location::where('company_id', Auth::user()?->company_id)->get();
        $assets = Asset::where('company_id', Auth::user()?->company_id)->get()->map(function ($asset) {
            $asset->display_name = $asset->name . ' (' . $asset->asset_tag . ')';
            return $asset;
        });
        
        $statusOptions = collect(AssetTransferStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label()
            ];
        });
        
        return view('livewire.asset-transfers.form', compact('locations', 'assets', 'statusOptions'));
    }
}