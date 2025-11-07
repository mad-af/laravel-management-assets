<?php

namespace App\Livewire\InsurancePolicies;

use Livewire\Attributes\Url;
use Livewire\Component;

class Drawer extends Component
{
    #[Url(as: 'action')] // ?action=create|edit
    public ?string $action = null;

    #[Url(as: 'policy_id')] // ?policy_id=123
    public ?string $policy_id = null;

    #[Url(as: 'asset_id')] // ?asset_id=123
    public ?string $asset_id = null;

    public bool $showDrawer = false;

    public ?string $editingPolicyId = null;

    public ?string $assetId = null;

    protected $listeners = [
        'close-drawer' => 'closeDrawer',
        'open-drawer' => 'openDrawer',
        'open-edit-drawer' => 'openEditDrawer',
        'policy-saved' => 'closeDrawer',
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

    public function updatedPolicyId()
    {
        $this->applyActionFromUrl();
    }

    protected function applyActionFromUrl(): void
    {
        if ($this->action === 'create') {
            $this->showDrawer = true;
            $this->editingPolicyId = null;
            $this->assetId = $this->asset_id;
        } elseif ($this->action === 'edit' && $this->policy_id) {
            $this->showDrawer = true;
            $this->editingPolicyId = $this->policy_id;
            $this->assetId = $this->asset_id;
        } // else: biarkan state tetap (jangan auto-tutup tiap update)
    }

    public function openEditDrawer($policyId)
    {
        $this->redirect(route('insurance-policies.index', [
            'action' => 'edit',
            'policy_id' => $policyId,
        ]), navigate: true);
    }

    public function openDrawer($assetId = '', $policyId = null)
    {
        if ($policyId) {
            $params = ['action' => 'edit', 'policy_id' => $policyId];
            if (! empty($assetId)) {
                $params['asset_id'] = $assetId;
            }
            $this->redirect(route('insurance-policies.index', $params), navigate: true);
        } else {
            $params = ['action' => 'create'];
            if (! empty($assetId)) {
                $params['asset_id'] = $assetId;
            }
            $this->redirect(route('insurance-policies.index', $params), navigate: true);
        }
    }

    public function closeDrawer()
    {
        $this->redirect(route('insurance-policies.index'), navigate: true);
    }

    public function editPolicy($policyId)
    {
        $this->openEditDrawer($policyId);
        $this->showDrawer = true;
    }

    public function handlePolicySaved()
    {
        $this->closeDrawer();
    }

    public function render()
    {
        return view('livewire.insurance-policies.drawer');
    }
}
