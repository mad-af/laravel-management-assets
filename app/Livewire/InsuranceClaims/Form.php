<?php

namespace App\Livewire\InsuranceClaims;

use App\Enums\InsuranceClaimIncidentType;
use App\Enums\InsuranceClaimSource;
use App\Enums\InsuranceClaimStatus;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    use Toast;

    public $claimId;

    public $isEdit = false;

    public ?string $policy_id = null;

    public ?string $asset_id = null;

    public string $claim_no = '';

    public ?string $incident_date = null;

    public string $incident_type = '';

    public ?string $incident_other = null;

    public ?string $description = null;

    public string $source = 'manual';

    public ?string $asset_maintenance_id = null; // optional, not exposed yet

    public string $status = 'draft';

    public ?string $amount_approved = null;

    public ?string $amount_paid = null;

    public array $policyOptions = [];

    public array $incidentTypeOptions = [];

    public array $statusOptions = [];

    public array $sourceOptions = [];

    protected function rules()
    {
        return [
            'policy_id' => ['nullable', 'exists:insurance_policies,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'claim_no' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'date'],
            'incident_type' => [
                'required',
                Rule::in(array_map(fn ($c) => $c->value, InsuranceClaimIncidentType::cases())),
            ],
            'incident_other' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source' => [
                'required',
                Rule::in(array_map(fn ($c) => $c->value, InsuranceClaimSource::cases())),
            ],
            'asset_maintenance_id' => ['nullable', 'exists:asset_maintenances,id'],
            'status' => [
                'required',
                Rule::in(array_map(fn ($c) => $c->value, InsuranceClaimStatus::cases())),
            ],
            'amount_approved' => ['nullable', 'numeric'],
            'amount_paid' => ['nullable', 'numeric'],
        ];
    }

    protected $listeners = [
        'editClaim' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($claimId = null)
    {
        $this->claimId = $claimId;
        $this->loadOptions();

        if ($claimId) {
            $this->isEdit = true;
            $this->loadClaim();
        }
    }

    protected function loadOptions(): void
    {
        $this->policyOptions = InsurancePolicy::with(['insurance', 'asset'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                $label = $p->policy_no;
                if ($p->insurance) {
                    $label .= ' - '.$p->insurance->name;
                }
                if ($p->asset) {
                    $label .= ' - '.$p->asset->name;
                }

                return ['value' => $p->id, 'label' => $label];
            })->toArray();

        $this->incidentTypeOptions = array_map(function ($c) {
            return ['value' => $c->value, 'label' => $c->label()];
        }, InsuranceClaimIncidentType::cases());

        $this->statusOptions = array_map(function ($c) {
            return ['value' => $c->value, 'label' => $c->label()];
        }, InsuranceClaimStatus::cases());

        $this->sourceOptions = array_map(function ($c) {
            return ['value' => $c->value, 'label' => $c->label()];
        }, InsuranceClaimSource::cases());
    }

    public function updatedPolicyId($value)
    {
        if ($value) {
            $policy = InsurancePolicy::with('asset')->find($value);
            $this->asset_id = $policy?->asset_id;
        }
    }

    public function loadClaim()
    {
        if ($this->claimId) {
            $claim = InsuranceClaim::find($this->claimId);
            if ($claim) {
                $this->policy_id = $claim->policy_id;
                $this->asset_id = $claim->asset_id;
                $this->claim_no = $claim->claim_no;
                $this->incident_date = optional($claim->incident_date)->format('Y-m-d');
                $this->incident_type = $claim->incident_type?->value ?? '';
                $this->incident_other = $claim->incident_other;
                $this->description = $claim->description;
                $this->source = $claim->source?->value ?? 'manual';
                $this->asset_maintenance_id = $claim->asset_maintenance_id;
                $this->status = $claim->status?->value ?? 'draft';
                $this->amount_approved = $claim->amount_approved;
                $this->amount_paid = $claim->amount_paid;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->claimId) {
                $claim = InsuranceClaim::findOrFail($this->claimId);
                $claim->update([
                    'policy_id' => $this->policy_id,
                    'asset_id' => $this->asset_id ?? InsurancePolicy::find($this->policy_id)?->asset_id,
                    'claim_no' => $this->claim_no,
                    'incident_date' => $this->incident_date,
                    'incident_type' => $this->incident_type,
                    'incident_other' => $this->incident_other,
                    'description' => $this->description,
                    'source' => $this->source,
                    'asset_maintenance_id' => $this->asset_maintenance_id,
                    'status' => $this->status,
                    'amount_approved' => $this->amount_approved,
                    'amount_paid' => $this->amount_paid,
                ]);
                $this->success('Klaim diperbarui!');
                $this->dispatch('claim-updated');
            } else {
                InsuranceClaim::create([
                    'policy_id' => $this->policy_id,
                    'asset_id' => $this->asset_id ?? InsurancePolicy::find($this->policy_id)?->asset_id,
                    'claim_no' => $this->claim_no,
                    'incident_date' => $this->incident_date,
                    'incident_type' => $this->incident_type,
                    'incident_other' => $this->incident_other,
                    'description' => $this->description,
                    'source' => $this->source,
                    'asset_maintenance_id' => $this->asset_maintenance_id,
                    'status' => $this->status,
                    'amount_approved' => $this->amount_approved,
                    'amount_paid' => $this->amount_paid,
                    'created_by' => Auth::id(),
                ]);
                $this->success('Klaim dibuat!');
                $this->dispatch('claim-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->policy_id = null;
        $this->asset_id = null;
        $this->claim_no = '';
        $this->incident_date = null;
        $this->incident_type = '';
        $this->incident_other = null;
        $this->description = null;
        $this->source = 'manual';
        $this->asset_maintenance_id = null;
        $this->status = 'draft';
        $this->amount_approved = null;
        $this->amount_paid = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.insurance-claims.form');
    }
}