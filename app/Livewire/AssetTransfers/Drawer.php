<?php

namespace App\Livewire\AssetTransfers;

use App\Models\AssetTransfer;
use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'transfer_id')] // ?transfer_id=123
    public ?string $transfer_id = null;

    public bool $showDrawer = false;

    public ?string $editingTransferId = null;

    public ?string $detailMode = null; // 'detail' | 'confirm'

    public ?AssetTransfer $detailTransfer = null;

    public array $detailInfoData = [];

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'open-transfer-detail' => 'openTransferDetail',
    ];

    public function mount()
    {
        $this->applyActionFromUrl(); // hanya sekali di initial load
    }

    // Dipanggil kalau kamu ubah action via property (akan auto update URL)
    public function updatedAction($value)
    {
        $this->applyActionFromUrl();
    }

    public function updatedTransferId()
    {
        $this->applyActionFromUrl();
    }

    private function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingTransferId = null;
        } elseif ($this->action === 'edit' && $this->transfer_id) {
            $this->showDrawer = true;
            $this->editingTransferId = $this->transfer_id;
        }
    }

    public function openEditDrawer($transferId)
    {
        $this->redirect(route('asset-transfers.index', [
            'action' => 'edit',
            'transfer_id' => $transferId,
        ]), navigate: true);
    }

    public function openDrawer()
    {
        $this->redirect(route('asset-transfers.index', [
            'action' => 'create',
        ]), navigate: true);
    }

    public function openTransferDetail(string $transferId, string $mode = 'detail'): void
    {
        $this->detailMode = $mode;
        $this->detailTransfer = AssetTransfer::query()
            ->with(['company', 'requestedBy', 'approvedBy', 'items.asset.branch', 'fromBranch', 'toBranch'])
            ->find($transferId);

        $assets = [];
        if ($this->detailTransfer) {
            $assets = $this->detailTransfer->items->map(function ($item) {
                $asset = $item->asset;

                return [
                    'name' => $asset?->name,
                    'tag_code' => $asset?->tag_code ?? $asset?->code,
                    'condition' => $asset?->condition,
                ];
            })->filter(fn ($x) => ! empty($x['name']))->values()->toArray();
        }

        $this->detailInfoData = [
            'transfer_no' => $this->detailTransfer?->transfer_no,
            'status' => $this->detailTransfer?->status,
            'type' => $this->detailTransfer?->type,
            'from_branch' => $this->detailTransfer?->fromBranch?->name,
            'to_branch' => $this->detailTransfer?->toBranch?->name,
            'requested_by' => $this->detailTransfer?->requestedBy?->name,
            'company' => $this->detailTransfer?->company?->name,
            'requested_at' => $this->detailTransfer?->requested_at,
            'description' => $this->detailTransfer?->description,
            'reason' => $this->detailTransfer?->reason,
            'notes' => $this->detailTransfer?->notes,
            'assets' => $assets,
        ];

        // pastikan drawer terbuka dengan konten detail, bukan form
        $this->showDrawer = true;
        $this->editingTransferId = null;
        $this->action = null;
        $this->transfer_id = null;
    }

    public function closeDrawer()
    {
        $this->redirect(route('asset-transfers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.asset-transfers.drawer');
    }
}
