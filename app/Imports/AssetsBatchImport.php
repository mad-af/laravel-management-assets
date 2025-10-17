<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsBatchImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    /**
     * Define heading row position (matches export template header row)
     */
    public function headingRow(): int
    {
        return 4;
    }

    /**
     * We don't process here; Livewire component will read collection via Excel::toCollection.
     */
    public function collection(Collection $rows)
    {
        // No-op: using Excel::toCollection to fetch rows instead
    }
}
