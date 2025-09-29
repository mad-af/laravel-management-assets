<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Branch;
use App\Enums\AssetStatus;
use App\Enums\AssetCondition;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Form extends Component
{
    use Toast;

    public $assetId;
    public $code = '';
    public $tag_code = '';
    public $name = '';
    public $category_id = '';
    public $location_id = '';
    public $status;
    public $condition;
    public $value = '';
    public $purchase_date = '';
    public $description = '';
    public $isEdit = false;

    protected $rules = [
        'code' => 'required|string|max:255',
        'tag_code' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'location_id' => 'required|exists:locations,id',
        'status' => 'required',
        'condition' => 'required',
        'value' => 'nullable|numeric|min:0',
        'purchase_date' => 'nullable|date',
        'description' => 'nullable|string',
    ];

    protected $listeners = [
        'editAsset' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($assetId = null)
    {
        $this->assetId = $assetId;
        $this->status = AssetStatus::ACTIVE->value;
        $this->condition = AssetCondition::GOOD->value;
        
        if ($assetId) {
            $this->isEdit = true;
            $this->loadAsset();
        } else {
            $this->generateCode();
        }
    }

    public function loadAsset()
    {
        if ($this->assetId) {
            $asset = Asset::find($this->assetId);
            if ($asset) {
                $this->code = $asset->code;
                $this->tag_code = $asset->tag_code;
                $this->name = $asset->name;
                $this->category_id = $asset->category_id;
                $this->location_id = $asset->location_id;
                $this->status = $asset->status->value;
                $this->condition = $asset->condition->value;
                $this->value = $asset->value;
                $this->purchase_date = $asset->purchase_date?->format('Y-m-d');
                $this->description = $asset->description;
            }
        }
    }

    public function generateCode()
    {
        if (!$this->isEdit && empty($this->code)) {
            $lastAsset = Asset::orderBy('created_at', 'desc')->first();
            $nextNumber = $lastAsset ? (int)substr($lastAsset->code, -3) + 1 : 1;
            $this->code = 'AST-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'tag_code' => $this->tag_code ?: null,
                'name' => $this->name,
                'category_id' => $this->category_id,
                'location_id' => $this->location_id,
                'status' => AssetStatus::from($this->status),
                'condition' => AssetCondition::from($this->condition),
                'value' => $this->value ?: null,
                'purchase_date' => $this->purchase_date ?: null,
                'description' => $this->description ?: null,
            ];

            if ($this->isEdit && $this->assetId) {
                $asset = Asset::find($this->assetId);
                $asset->update($data);
                $this->success('Asset updated successfully!');
                $this->dispatch('asset-updated');
            } else {
                Asset::create($data);
                $this->success('Asset created successfully!');
                $this->dispatch('asset-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->code = '';
        $this->tag_code = '';
        $this->name = '';
        $this->category_id = '';
        $this->location_id = '';
        $this->status = AssetStatus::ACTIVE->value;
        $this->condition = AssetCondition::GOOD->value;
        $this->value = '';
        $this->purchase_date = '';
        $this->description = '';
        $this->resetValidation();
        
        if (!$this->isEdit) {
            $this->generateCode();
        }
    }

    public function render()
    {
        $categories = Category::active()->orderBy('name')->get();
        $locations = Branch::orderBy('name')->get();
        
        $statuses = collect(AssetStatus::cases())->map(function ($status) {
            return (object) [
                'value' => $status->value,
                'label' => $status->label()
            ];
        });
        
        $conditions = collect(AssetCondition::cases())->map(function ($condition) {
            return (object) [
                'value' => $condition->value,
                'label' => $condition->label()
            ];
        });

        return view('livewire.assets.form', compact('categories', 'locations', 'statuses', 'conditions'));
    }
}