<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetsBatchTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Empty collection – template only with headings
        return new Collection();
    }

    public function headings(): array
    {
        return [
            'code',
            'name',
            'category_id',
            'branch_id',
            'value',
            'brand',
            'model',
            'status',       // active|inactive|lost|maintenance|on_loan
            'condition',    // good|fair|poor
            'purchase_date',// YYYY-MM-DD
            'description',
        ];
    }
}