<?php

namespace App\Livewire\InsuranceClaims;

use App\Enums\InsuranceClaimIncidentType;
use App\Enums\InsuranceClaimSource;
use App\Enums\InsuranceClaimStatus;
use App\Enums\InsuranceStatus;
use App\Models\Asset;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

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

    public string $source = InsuranceClaimSource::MANUAL->value;

    public ?string $asset_maintenance_id = null; // optional, not exposed yet

    public string $status = InsuranceClaimStatus::SUBMITTED->value;

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
        'imageUploaded' => 'handleImageUploaded',
        'imageRemoved' => 'handleImageRemoved',
        'imageFinalized' => 'handleImageFinalized',
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

    // #[On('combobox-load-policies')]
    protected function loadOptions(): void
    {
        $this->loadPolicyOptions();

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

    #[On('combobox-load-policies')]
    public function loadPolicyOptions($search = ''): void
    {
        $assets = Asset::forBranch()
            ->with([
                'latestActiveInsurancePolicy' => function ($q) {
                    $q->where('status', InsuranceStatus::ACTIVE->value);
                },
                'latestActiveInsurancePolicy.insurance',
            ])
            ->whereHas('insurancePolicies', function ($q) {
                $q->where('status', InsuranceStatus::ACTIVE->value);
            })
            // 🔍 Tambahkan pencarian nama aset & nomor polis
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhereHas('insurancePolicies', function ($sub2) use ($search) {
                            $sub2->where('policy_no', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $this->policyOptions = $assets->map(function ($asset) {
            $policy = $asset->latestActiveInsurancePolicy;
            if (! $policy) {
                return null;
            }

            return [
                'id' => $policy->id,
                'policy_no' => (string) $policy->policy_no,
                'insurance_name' => optional($policy->insurance)->name,
                'asset_name' => (string) $asset->name,
            ];
        })
            ->filter()
            ->values()
            ->toArray();

        $this->dispatch('combobox-set-policies', $this->policyOptions);
    }

    public function updatedPolicyId($value)
    {
        if ($value) {
            $policy = InsurancePolicy::with('asset')->find($value);
            $this->asset_id = $policy?->asset_id;
        }
    }

    public ?string $currentClaimImage = null;

    public ?string $tempImagePath = null;

    public bool $removeCurrentImage = false;

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
                $this->source = $claim->source?->value ?? InsuranceClaimSource::MANUAL->value;
                $this->asset_maintenance_id = $claim->asset_maintenance_id;
                $this->status = $claim->status?->value ?? InsuranceClaimStatus::SUBMITTED->value;
                $this->amount_approved = $claim->amount_approved;
                $this->amount_paid = $claim->amount_paid;

                // Prefill currentClaimImage from claim_documents (first 'photo' or first item)
                $docs = is_array($claim->claim_documents) ? $claim->claim_documents : (json_decode($claim->claim_documents, true) ?? []);
                $current = null;
                foreach ($docs as $d) {
                    if (($d['doc_type'] ?? '') === 'photo' && ! empty($d['file_path'])) {
                        $current = $d['file_path'];
                        break;
                    }
                }
                if (! $current && ! empty($docs)) {
                    $current = $docs[0]['file_path'] ?? null;
                }
                $this->currentClaimImage = $current;
            }
        }
    }

    public function save()
    {
        $this->validate();

        // If there is a temp image, ask child component to finalize first
        if ($this->tempImagePath) {
            $this->dispatch('finalizeImageUpload');

            return;
        }

        try {
            if ($this->isEdit && $this->claimId) {
                $claim = InsuranceClaim::findOrFail($this->claimId);

                // Build claim_documents payload
                $existingDocs = is_array($claim->claim_documents) ? $claim->claim_documents : (json_decode($claim->claim_documents, true) ?? []);
                // Remove existing 'photo' entries if flagged or to replace
                $docs = array_values(array_filter($existingDocs, function ($d) {
                    return ($d['doc_type'] ?? '') !== 'photo';
                }));
                if ($this->currentClaimImage && ! $this->removeCurrentImage) {
                    $docs[] = [
                        'doc_type' => 'photo',
                        'file_path' => $this->currentClaimImage,
                        'description' => 'Bukti klaim',
                    ];
                }

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
                    'claim_documents' => $docs,
                ]);
                $this->success('Klaim diperbarui!');
                $this->dispatch('claim-updated');
            } else {
                // Build claim_documents for create
                $docs = [];
                if ($this->currentClaimImage && ! $this->removeCurrentImage) {
                    $docs[] = [
                        'doc_type' => 'photo',
                        'file_path' => $this->currentClaimImage,
                        'description' => 'Bukti klaim',
                    ];
                }

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
                    'claim_documents' => $docs,
                ]);
                $this->success('Klaim dibuat!');
                $this->dispatch('claim-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            dd($e);
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function handleImageUploaded($tempPath)
    {
        $this->tempImagePath = $tempPath;
        $this->removeCurrentImage = false;
    }

    public function handleImageRemoved()
    {
        $this->tempImagePath = null;
        $this->currentClaimImage = null;
        $this->removeCurrentImage = true;
    }

    public function handleImageFinalized($path)
    {
        $this->currentClaimImage = $path;
        $this->tempImagePath = null;
        $this->removeCurrentImage = false;
        // Proceed with save after finalize
        $this->save();
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
        $this->source = InsuranceClaimSource::MANUAL->value;
        $this->asset_maintenance_id = null;
        $this->status = InsuranceClaimStatus::SUBMITTED->value;
        $this->amount_approved = null;
        $this->amount_paid = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.insurance-claims.form');
    }
}
