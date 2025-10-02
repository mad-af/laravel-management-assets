<?php

namespace App\Livewire\Assets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Category;
use App\Services\ImageUploadService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast, WithFileUploads;

    public $assetId;

    public $code = '';

    public $tag_code = '';

    public $image;

    public $imageFile;

    public $tempImagePath;

    public $name = '';

    public $category_id = '';

    public $branch_id = '';

    public $brand = '';

    public $model = '';

    public $status;

    public $condition;

    public $value = '';

    public $purchase_date = '';

    public $description = '';

    public $isEdit = false;

    public $codeIsset = false;

    protected $rules = [
        'code' => 'required|string|max:255',
        'tag_code' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'branch_id' => 'required|exists:branches,id',
        'brand' => 'nullable|string|max:255',
        'model' => 'nullable|string|max:255',
        'image' => 'nullable|string|max:255',
        'imageFile' => 'nullable|image|max:2048',
        'status' => 'required',
        'condition' => 'required',
        'value' => 'nullable|numeric|min:0',
        'purchase_date' => 'nullable|date',
        'description' => 'nullable|string',
    ];

    protected $listeners = [
        'editAsset' => 'edit',
        'resetForm' => 'resetForm',
        'imageUploaded' => 'handleImageUploaded',
        'imageRemoved' => 'handleImageRemoved',
    ];

    public function mount($assetId = null)
    {
        $this->assetId = $assetId;
        $this->status = AssetStatus::ACTIVE->value;
        $this->condition = AssetCondition::GOOD->value;

        // Get branch_id from session using helper
        $this->branch_id = session_get(\App\Support\SessionKey::BranchId);

        if ($assetId) {
            $this->isEdit = true;
            $this->loadAsset();
        } else {
            // $this->generateCode();
        }
    }

    /**
     * Handle image file upload when file is selected
     */
    public function updatedImageFile()
    {
        // Reset tempImagePath first
        $this->tempImagePath = null;

        $this->validate([
            'imageFile' => 'required|image|max:2048',
        ]);

        try {
            $imageUploadService = new ImageUploadService;

            // Upload to temporary folder
            $tempPath = $imageUploadService->uploadTemporary($this->imageFile, [
                'quality' => 85,
                'max_width' => 1200,
                'max_height' => 800,
                'convert_to_webp' => true,
            ]);

            if ($tempPath) {
                $this->tempImagePath = $tempPath;
                $this->success('Gambar berhasil diupload sementara');

                // Force re-render
                $this->dispatch('$refresh');
            } else {
                $this->error('Gagal mengupload gambar');
            }

        } catch (\Exception $e) {
            $this->error('Gagal mengupload gambar: '.$e->getMessage());
            $this->tempImagePath = null;
        }

        $this->dispatch('$refresh');
    }

    /**
     * Remove uploaded image
     */
    public function removeImage()
    {
        if ($this->tempImagePath) {
            $imageUploadService = new ImageUploadService;
            $imageUploadService->delete($this->tempImagePath);
            $this->tempImagePath = null;
        }

        $this->imageFile = null;
        $this->image = '';

        $this->success('Gambar berhasil dihapus');

        // Force re-render
        $this->dispatch('$refresh');
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
                $this->branch_id = $asset->branch_id;
                $this->brand = $asset->brand;
                $this->model = $asset->model;
                $this->image = $asset->image;
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
        if (! $this->isEdit) {
            try {
                // Generate asset code using helper function
                $this->code = generate_asset_code($this->category_id, $this->branch_id);
            } catch (\Exception $e) {

            }
        }
    }

    // Auto-generate code when category changes
    public function updatedCategoryId()
    {
        if (! $this->isEdit && ! empty($this->category_id) && ! empty($this->branch_id)) {
            $this->generateCode();
        }
    }

    public function generateTagCode()
    {
        if (empty($this->tag_code)) {
            try {
                $this->tag_code = generate_asset_tag_code();
            } catch (\Exception $e) {
                $this->error('Failed to generate tag code: '.$e->getMessage());
            }
        }
    }

    public function generateBothCodes()
    {
        $this->generateCode();
        $this->generateTagCode();
    }

    public function validateCodes()
    {
        $errors = [];

        // Validate asset code
        if (! empty($this->code) && ! validate_asset_code($this->code)) {
            $errors[] = 'Asset code format is invalid';
        }

        // Validate tag code
        if (! empty($this->tag_code) && ! validate_asset_tag_code($this->tag_code)) {
            $errors[] = 'Tag code format is invalid';
        }

        if (! empty($errors)) {
            foreach ($errors as $error) {
                $this->error($error);
            }

            return false;
        }

        return true;
    }

    public function save()
    {
        $this->validate();

        // Validate codes using helper functions
        if (! $this->validateCodes()) {
            return; // Stop if validation fails
        }

        try {
            $imageUploadService = new ImageUploadService;
            $finalImagePath = $this->image;

            // Handle image upload from temporary to permanent storage
            if ($this->tempImagePath) {
                $finalImagePath = $imageUploadService->moveFromTemporary($this->tempImagePath, 'assets');
                $this->tempImagePath = null; // Clear temp path after moving
            }

            $data = [
                'code' => $this->code,
                'tag_code' => $this->tag_code ?: null,
                'name' => $this->name,
                'category_id' => $this->category_id,
                'branch_id' => $this->branch_id,
                'brand' => $this->brand ?: null,
                'model' => $this->model ?: null,
                'image' => $finalImagePath ?: null,
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
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function resetForm()
    {
        // Clean up temporary image if exists
        if ($this->tempImagePath) {
            $imageUploadService = new ImageUploadService;
            $imageUploadService->delete($this->tempImagePath);
            $this->tempImagePath = null;
        }

        $this->code = '';
        $this->tag_code = '';
        $this->name = '';
        $this->category_id = '';
        $this->branch_id = session_get(\App\Support\SessionKey::BranchId);
        $this->brand = '';
        $this->model = '';
        $this->image = '';
        $this->imageFile = null;
        $this->status = AssetStatus::ACTIVE->value;
        $this->condition = AssetCondition::GOOD->value;
        $this->value = '';
        $this->purchase_date = '';
        $this->description = '';
        $this->resetValidation();

        if (! $this->isEdit) {
            $this->generateCode();
        }
    }

    public function handleImageUploaded($tempPath)
    {
        $this->tempImagePath = $tempPath;
    }

    public function handleImageRemoved()
    {
        $this->tempImagePath = null;
    }

    public function render()
    {
        $categories = Category::active()->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $statuses = collect(AssetStatus::cases())->map(function ($status) {
            return (object) [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        });

        $conditions = collect(AssetCondition::cases())->map(function ($condition) {
            return (object) [
                'value' => $condition->value,
                'label' => $condition->label(),
            ];
        });

        return view('livewire.assets.form', compact('categories', 'branches', 'statuses', 'conditions'));
    }
}
