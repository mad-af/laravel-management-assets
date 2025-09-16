<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use App\Models\Company;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $categoryId;
    public $name = '';
    public $company_id = '';
    public $is_active = true;
    public $isEdit = false;
    public $allCompanies = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'company_id' => 'required|exists:companies,id',
        'is_active' => 'boolean',
    ];

    protected $listeners = [
        'editCategory' => 'edit',
        'resetForm' => 'resetForm'
    ];

    public function mount($categoryId = null)
    {
        $this->categoryId = $categoryId;
        $this->allCompanies = Company::where('is_active', true)->get();
        
        if ($categoryId) {
            $this->isEdit = true;
            $this->loadCategory();
        }
    }

    public function loadCategory()
    {
        if ($this->categoryId) {
            $category = Category::find($this->categoryId);
            if ($category) {
                $this->name = $category->name;
                $this->company_id = $category->company_id;
                $this->is_active = $category->is_active;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit && $this->categoryId) {
                $category = Category::find($this->categoryId);
                $category->update([
                    'name' => $this->name,
                    'company_id' => $this->company_id,
                    'is_active' => $this->is_active,
                ]);
                $this->success('Category updated successfully!');
                $this->dispatch('category-updated');
            } else {
                Category::create([
                    'name' => $this->name,
                    'company_id' => $this->company_id,
                    'is_active' => $this->is_active,
                ]);
                $this->success('Category created successfully!');
                $this->dispatch('category-saved');
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->company_id = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.categories.form');
    }
}