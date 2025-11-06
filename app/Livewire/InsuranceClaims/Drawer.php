<?php

namespace App\Livewire\InsuranceClaims;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'claim_id')] // ?claim_id=123
    public ?string $claim_id = null;

     #[Url(as: 'policy_id')] // ?policy_id=123
    public ?string $policy_id = null;

    public bool $showDrawer = false;

    public ?string $editingClaimId = null;

    public ?string $editingPolicyId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'claim-saved' => 'closeDrawer',
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

    public function updatedClaimId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingClaimId = null;
        } elseif ($this->action === 'edit' && $this->claim_id) {
            $this->showDrawer = true;
            $this->editingClaimId = $this->claim_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)

        if ($this->policy_id) {
            $this->editingPolicyId = $this->policy_id;
        }
    }

    public function openEditDrawer($claimId)
    {
        $this->action = 'edit';
        $this->claim_id = $claimId;
        $this->applyActionFromUrl();
    }

    public function openDrawer($claimId = null)
    {
        if ($claimId) {
            $this->action = 'edit';
            $this->claim_id = $claimId;
        } else {
            $this->action = 'create';
        }
        $this->applyActionFromUrl();
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->editingClaimId = null;
        $this->editingPolicyId = null;
        // $this->dispatch('resetForm');

        // hapus query di URL (Url-bound akan pushState)
        $this->action = null;
        $this->claim_id = null;
        $this->policy_id = null;
    }

    public function editClaim($claimId)
    {
        $this->openEditDrawer($claimId);
        $this->showDrawer = true;
    }

    public function handleClaimSaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.insurance-claims.drawer');
    }
}
