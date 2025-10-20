<?php

namespace App\Livewire\AssetLoans;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Employee;
use App\Support\SessionKey;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $assetLoanId;

    public $asset_id = '';

    public $employee_id = '';

    public $checkout_at = '';

    public $due_at = '';

    public $checkin_at = '';

    public $condition_out;

    public $condition_in;

    public $notes = '';

    public $isEdit = false;

    public $employees = [];

    public $assets = [];

    protected $rules = [
        'asset_id' => 'required|exists:assets,id',
        'employee_id' => 'required|exists:employees,id',
        'checkout_at' => 'required|date',
        'due_at' => 'required|date|after:checkout_at',
        'checkin_at' => 'nullable|date|after_or_equal:checkout_at',
        'condition_out' => 'nullable',
        'condition_in' => 'nullable',
        'notes' => 'nullable|string',
    ];

    protected $listeners = [
        'editAssetLoan' => 'edit',
        'resetEditForm' => 'resetForm',
    ];

    public function mount($assetLoanId = null, $assetId = null)
    {
        if (! empty($assetId)) {
            $this->asset_id = $assetId;
        }

        $this->assetLoanId = $assetLoanId;
        $this->checkout_at = now()->format('Y-m-d');
        $this->due_at = now()->addYear()->format('Y-m-d');
        $this->loadEmployees();
        $this->loadAssets();

        if ($assetLoanId) {
            $this->isEdit = true;
            $this->loadAssetLoan();
        }
    }

    #[On('combobox-load-employees')]
    public function loadEmployees($search = '')
    {
        $branchId = session_get(SessionKey::BranchId);
        $query = Employee::query()
            ->where('branch_id', $branchId);

        if (! empty($search)) {
            $query->where('full_name', 'like', "%$search%");
        }

        $this->employees = $query->orderBy('full_name')
            ->get(['id', 'full_name', 'email'])
            ->toArray();

        // Kirim data options terbaru ke combobox instance bernama 'employees'
        $this->dispatch('combobox-set-employees', $this->employees);
    }

    #[On('combobox-load-assets')]
    public function loadAssets($search = '')
    {
        $branchId = session_get(SessionKey::BranchId);
        $query = Asset::forBranch($branchId)->available();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('tag_code', 'like', "%$search%");
            });
        }

        $this->assets = $query->orderBy('name')
            ->get(['id', 'name', 'code', 'tag_code', 'image'])
            ->toArray();

        // Kirim hasil pencarian ke combobox 'assets'
        $this->dispatch('combobox-set-assets', $this->assets);
    }

    public function loadAssetLoan()
    {
        if ($this->assetLoanId) {
            $assetLoan = AssetLoan::find($this->assetLoanId);
            if ($assetLoan) {
                $this->asset_id = $assetLoan->asset_id;
                $this->employee_id = $assetLoan->employee_id;
                $this->checkout_at = $assetLoan->checkout_at->format('Y-m-d');
                $this->due_at = $assetLoan->due_at->format('Y-m-d');
                $this->checkin_at = $assetLoan->checkin_at?->format('Y-m-d');
                $this->condition_out = $assetLoan->condition_out?->value;
                $this->condition_in = $assetLoan->condition_in?->value;
                $this->notes = $assetLoan->notes;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'asset_id' => $this->asset_id,
                'employee_id' => $this->employee_id,
                'checkout_at' => $this->checkout_at,
                'due_at' => $this->due_at,
                'checkin_at' => $this->checkin_at ?: null,
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEdit && $this->assetLoanId) {
                $assetLoan = AssetLoan::find($this->assetLoanId);
                $assetLoan->update($data);
                $this->success('Asset loan updated successfully!');
                
            } else {
                AssetLoan::create($data);
                $this->success('Asset loan created successfully!');
            }
            $this->dispatch('table-refresh');
            $this->dispatch('close-drawer');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function returnAsset()
    {
        if ($this->isEdit && $this->assetLoanId) {
            $this->checkin_at = now()->format('Y-m-d');
            if (! $this->condition_in) {
                $this->condition_in = $this->condition_out;
            }
        }
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->asset_id = '';
        $this->employee_id = '';
        $this->checkout_at = now()->format('Y-m-d');
        $this->due_at = now()->addDays(7)->format('Y-m-d');
        $this->checkin_at = null;
        $this->condition_in = null;
        $this->notes = '';
        $this->resetValidation();
        // $this->dispatch('combobox-clear');
    }

    public function render()
    {
        // $branchId = session_get(SessionKey::BranchId);
        // $assets = Asset::forBranch($branchId)->available()->orderBy('name')->get(['id', 'name']);

        return view('livewire.asset-loans.form');
    }
}
