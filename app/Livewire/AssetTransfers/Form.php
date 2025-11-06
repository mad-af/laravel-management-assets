<?php

namespace App\Livewire\AssetTransfers;

use App\Enums\AssetTransferStatus;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Branch;
use App\Support\SessionKey;
use App\Traits\WithAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast, WithAlert;

    public $transferId;

    public $transfer_no = '';

    public $reason = '';

    public $from_branch_id = '';

    public $to_branch_id = '';

    public $status = 'shipped';

    public $delivery_at = '';

    public $notes = '';

    public $isEdit = false;

    // Asset items
    public $items = [];

    public $assets = [];

    protected $rules = [
        'reason' => 'required|string|max:500',
        'from_branch_id' => 'required|exists:branches,id',
        'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
        'status' => 'required|string',
        'delivery_at' => 'nullable|date',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.asset_id' => 'required|exists:assets,id',
        'items.*.from_branch_id' => 'nullable|exists:branches,id',
        'items.*.to_branch_id' => 'nullable|exists:branches,id',
    ];

    protected $listeners = [
        'editTransfer' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function updated($propertyName)
    {
        if ($propertyName === 'from_branch_id') {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['from_branch_id'] = $this->from_branch_id;
            }
            $this->loadAssets();
        }

        if ($propertyName === 'to_branch_id') {
            foreach ($this->items as $index => $item) {
                $this->items[$index]['to_branch_id'] = $this->to_branch_id;
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
            // Default from branch dari session
            $currentBranchId = session_get(SessionKey::BranchId);
            if ($currentBranchId) {
                $this->from_branch_id = $currentBranchId;
            }
            // Add default item
            $this->addItem();
            $this->loadAssets();
        }
    }

    public function loadTransfer()
    {
        if ($this->transferId) {
            $transfer = AssetTransfer::with('items')->find($this->transferId);
            if ($transfer) {
                $this->transfer_no = $transfer->transfer_no;
                $this->reason = $transfer->reason;
                $this->from_branch_id = $transfer->from_branch_id;
                $this->to_branch_id = $transfer->to_branch_id;
                $this->status = $transfer->status->value;
                $this->delivery_at = $transfer->delivery_at?->format('Y-m-d\\TH:i');
                $this->notes = $transfer->notes;

                $this->items = $transfer->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'uid' => (string) \Illuminate\Support\Str::uuid(),
                        'asset_id' => $item->asset_id,
                        'notes' => $item->notes,
                        'from_branch_id' => $this->from_branch_id,
                        'to_branch_id' => $this->to_branch_id,
                    ];
                })->toArray();
            }
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'uid' => (string) \Illuminate\Support\Str::uuid(),
            'asset_id' => '',
            'from_branch_id' => $this->from_branch_id,
            'to_branch_id' => $this->to_branch_id,
        ];
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    #[On('combobox-load-assets')]
    public function loadAssets($search = '')
    {
        $query = Asset::forBranch()->available();

        // Ambil asset_id yang sudah dipilih untuk dikecualikan dari opsi
        $selectedAssetIds = collect($this->items)
            ->pluck('asset_id')
            ->filter()
            ->values()
            ->all();

        if (! empty($selectedAssetIds)) {
            $query->whereNotIn('id', $selectedAssetIds);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('tag_code', 'like', "%$search%");
            });
        }

        $this->assets = $query->orderBy('name')
            ->get(['id', 'name', 'code', 'tag_code', 'image'])
            ->toArray();

        // Kirim hasil pencarian ke semua instance combobox bernama 'assets'
        $this->dispatch('combobox-set-assets', $this->assets);
    }

    public function resetForm()
    {
        $this->transfer_no = '';
        $this->reason = '';
        $this->from_branch_id = '';
        $this->to_branch_id = '';
        $this->status = 'shipped';
        $this->delivery_at = '';
        $this->notes = '';
        $this->items = [];
        $this->resetValidation();

        if (! $this->isEdit) {
            $this->addItem();
        }
    }

    private function generateTransferNo(): string
    {
        return 'TRF-'.date('Ymd').'-'.strtoupper(Str::random(4));
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $currentCompanyId = session_get(SessionKey::CompanyId);

                if ($this->isEdit && $this->transferId) {
                    $transfer = AssetTransfer::with('items')->findOrFail($this->transferId);

                    $transfer->update([
                        'reason' => $this->reason,
                        'from_branch_id' => $this->from_branch_id,
                        'to_branch_id' => $this->to_branch_id,
                        'status' => AssetTransferStatus::from($this->status),
                        'accepted_at' => null,
                        'delivery_at' => $this->delivery_at ?: null,
                        'notes' => $this->notes,
                    ]);

                    // Reset items
                    $transfer->items()->delete();

                    foreach ($this->items as $item) {
                        $transfer->items()->create([
                            'asset_id' => $item['asset_id'],
                            'from_branch_id' => $this->from_branch_id,
                            'to_branch_id' => $this->to_branch_id,
                            'notes' => $item['notes'] ?? '',
                        ]);
                    }

                    $this->dispatch('transfer-updated');
                } else {
                    $transfer = AssetTransfer::create([
                        'company_id' => $currentCompanyId,
                        'transfer_no' => $this->generateTransferNo(),
                        'reason' => $this->reason,
                        'status' => AssetTransferStatus::from($this->status),
                        'delivery_by' => Auth::id(),
                        'from_branch_id' => $this->from_branch_id,
                        'to_branch_id' => $this->to_branch_id,
                        'accepted_at' => null,
                        'delivery_at' => $this->delivery_at ?: null,
                        'notes' => $this->notes,
                    ]);

                    foreach ($this->items as $item) {
                        $transfer->items()->create([
                            'asset_id' => $item['asset_id'],
                            'from_branch_id' => $this->from_branch_id,
                            'to_branch_id' => $this->to_branch_id,
                            'notes' => $item['notes'] ?? '',
                        ]);
                    }

                    $this->dispatch('transfer-saved');
                }
            });

            $this->success($this->isEdit ? 'Transfer aset berhasil diupdate!' : 'Transfer aset berhasil dibuat!');
            $this->dispatch('close-drawer');
        } catch (\Exception $e) {
            dd($e);
            Log::error('Asset transfer save failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'transfer_id' => $this->transferId,
                'data' => [
                    'reason' => $this->reason,
                    'from_branch_id' => $this->from_branch_id,
                    'to_branch_id' => $this->to_branch_id,
                    'items' => $this->items,
                ],
            ]);

            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $currentBranchId = session_get(SessionKey::BranchId);

        // From branch: hanya cabang di session, disabled di view
        $fromBranches = Branch::query()
            ->where('is_active', true)
            ->when($currentBranchId, fn ($q) => $q->where('id', $currentBranchId))
            ->get(['id', 'name']);

        // To branch: semua cabang aktif kecuali cabang di session, dikelompokkan per perusahaan
        $toBranches = Branch::query()
            ->with('company')
            ->where('is_active', true)
            ->when($currentBranchId, fn ($q) => $q->where('id', '!=', $currentBranchId))
            ->orderBy('name')
            ->get();

        $toGroupedBranches = [];
        foreach ($toBranches as $branch) {
            $groupKey = $branch->company?->name ?? 'Perusahaan';
            $toGroupedBranches[$groupKey][] = [
                'id' => $branch->id,
                'name' => $branch->name,
            ];
        }

        $statusOptions = collect(AssetTransferStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        });

        return view('livewire.asset-transfers.form', compact('fromBranches', 'toGroupedBranches', 'statusOptions'))
            ->with('transferId', $this->transferId)
            ->with('isEdit', $this->isEdit);
    }
}
