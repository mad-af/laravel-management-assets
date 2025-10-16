<?php

namespace App\Exports;

use App\Exports\Sheets\AssetsBatchReferenceSheet;
use App\Exports\Sheets\AssetsBatchTemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssetsBatchTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AssetsBatchTemplateSheet,
            new AssetsBatchReferenceSheet,
        ];
    }
}
