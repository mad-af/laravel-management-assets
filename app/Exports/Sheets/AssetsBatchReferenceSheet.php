<?php

namespace App\Exports\Sheets;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssetsBatchReferenceSheet implements FromView, WithTitle
{
    protected Collection $categories;
    protected Collection $statuses;
    protected Collection $conditions;

    public function __construct()
    {
        $this->categories = Category::active()->orderBy('name')->get(['id', 'name']);
        $this->statuses = collect(AssetStatus::cases())->map(fn ($s) => [
            'value' => $s->value,
            'label' => $s->label(),
        ]);
        $this->conditions = collect(AssetCondition::cases())->map(fn ($c) => [
            'value' => $c->value,
            'label' => $c->label(),
        ]);
    }

    public function title(): string
    {
        return 'Referensi';
    }

    public function view(): View
    {
        return view('excel.assets-batch-reference', [
            'categories' => $this->categories,
            'statuses' => $this->statuses,
            'conditions' => $this->conditions,
        ]);
    }
}