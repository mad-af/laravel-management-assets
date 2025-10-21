<?php

namespace App\Exports;

use App\Models\AssetMaintenance;
use App\Exports\Sheets\MaintenancesMonthlySheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class MaintenancesMonthlyExport implements WithMultipleSheets
{
    protected string $branchId;
    protected Collection $maintenances;

    public function __construct(string $branchId)
    {
        $this->branchId = $branchId;

        $this->maintenances = AssetMaintenance::with(['asset', 'employee'])
            ->whereHas('asset', function ($q) {
                $q->where('branch_id', $this->branchId);
            })
            ->orderBy('started_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function sheets(): array
    {
        // Group by month (prefer started_at; fallback created_at)
        $groups = $this->maintenances->groupBy(function ($m) {
            $date = $m->started_at ?: $m->created_at;
            return ($date instanceof Carbon ? $date : Carbon::parse($date))->format('Y-m');
        })->sortKeysDesc();

        $sheets = [];

        foreach ($groups as $ym => $items) {
            [$year, $month] = explode('-', $ym);
            $date = Carbon::createFromDate((int)$year, (int)$month, 1)->locale('id');
            $title = $date->translatedFormat('F Y'); // e.g., "Januari 2025"

            $sheets[] = new MaintenancesMonthlySheet($title, $items);
        }

        // If no data, still provide an empty sheet
        if (empty($sheets)) {
            $sheets[] = new MaintenancesMonthlySheet('Tidak Ada Data', collect());
        }

        return $sheets;
    }
}