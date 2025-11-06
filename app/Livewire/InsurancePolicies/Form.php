<?php

namespace App\Livewire\InsurancePolicies;

use App\Enums\InsurancePolicyType;
use App\Models\Asset;
use App\Models\Insurance;
use App\Models\InsurancePolicy;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $policyId;

    public $asset_id = '';

    public $insurance_id = '';

    public $policy_no = '';

    public $policy_type = '';

    public $start_date = '';

    public $end_date = '';

    public $notes = '';

    public $isEdit = false;

    public array $assets = [];

    public array $insurances = [];

    public array $policyTypes = [];

    protected $listeners = [
        'resetForm' => 'resetForm',
        'combobox-load-asset' => 'loadAssetOptions',
    ];

    protected function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'insurance_id' => 'required|exists:insurances,id',
            'policy_no' => 'required|string|max:255',
            'policy_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function mount($assetId = null, $policyId = null)
    {
        $this->policyId = $policyId;
        $this->asset_id = $assetId;

        $this->insurances = Insurance::orderBy('name')->get(['id', 'name'])->toArray();
        $this->policyTypes = collect(InsurancePolicyType::cases())
            ->map(fn ($e) => ['value' => $e->value, 'label' => $e->label()])
            ->toArray();

        $this->loadAssetOptions();

        if ($policyId) {
            $this->isEdit = true;
            $this->loadPolicy();
        }
    }

    public function loadPolicy()
    {
        if ($this->policyId) {
            $policy = InsurancePolicy::find($this->policyId);
            if ($policy) {
                $this->asset_id = $policy->asset_id;
                $this->insurance_id = $policy->insurance_id;
                $this->policy_no = $policy->policy_no;
                $this->policy_type = $policy->policy_type->value;
                $this->start_date = optional($policy->start_date)->format('Y-m-d');
                $this->end_date = optional($policy->end_date)->format('Y-m-d');
                $this->notes = $policy->notes;
            }
        }
    }

    public function save()
    {
        
        try {
            $this->validate();
            if ($this->isEdit && $this->policyId) {
                $policy = InsurancePolicy::findOrFail($this->policyId);
                $policy->update([
                    'asset_id' => $this->asset_id,
                    'insurance_id' => $this->insurance_id,
                    'policy_no' => $this->policy_no,
                    'policy_type' => $this->policy_type,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'notes' => $this->notes,
                ]);
                $this->success('Polis asuransi berhasil diperbarui!');
                $this->dispatch('policy-updated');
            } else {
                InsurancePolicy::create([
                    'asset_id' => $this->asset_id,
                    'insurance_id' => $this->insurance_id,
                    'policy_no' => $this->policy_no,
                    'policy_type' => $this->policy_type,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'notes' => $this->notes,
                ]);
                $this->success('Polis asuransi berhasil dibuat!');
                $this->dispatch('policy-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->asset_id = '';
        $this->insurance_id = '';
        $this->policy_no = '';
        $this->policy_type = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->notes = '';
        $this->resetValidation();
    }

    #[On('combobox-load-assets')]    
    public function loadAssetOptions($search = '')
    {
        $query = Asset::forBranch()->withoutInsurancePolicy()->orderBy('name');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%')
                    ->orWhere('tag_code', 'like', '%'.$search.'%');
            });
        }
        $this->assets = $query->limit(20)->get(['id', 'name', 'tag_code', 'code', 'image'])->toArray();

        $this->dispatch('combobox-set-assets', $this->assets);
    }

    public function render()
    {
        return view('livewire.insurance-policies.form');
    }
}
