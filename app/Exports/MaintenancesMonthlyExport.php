<?php

namespace App\Exports;

use App\Exports\Sheets\MaintenancesMonthlySheet;
use App\Models\AssetMaintenance;
use App\Support\MaintenanceServiceCheck;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MaintenancesMonthlyExport implements WithMultipleSheets
{
    protected string $branchId;

    protected Collection $maintenances;

    /**
     * Service schedule check per maintenance id.
     *
     * @var array<string, array>
     */
    protected array $serviceChecks = [];

    public function __construct(string $branchId)
    {
        $this->branchId = $branchId;

        $this->maintenances = AssetMaintenance::with(['asset.vehicleProfile', 'employee'])
            ->whereHas('asset', function ($q) {
                $q->where('branch_id', $this->branchId);
            })
            ->orderBy('started_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->serviceChecks = $this->buildServiceChecks();
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
            $date = Carbon::createFromDate((int) $year, (int) $month, 1)->locale('id');
            $title = $date->translatedFormat('F Y'); // e.g., "Januari 2025"

            $sheets[] = new MaintenancesMonthlySheet($title, $items, $this->serviceChecks);
        }

        // If no data, still provide an empty sheet
        if (empty($sheets)) {
            $sheets[] = new MaintenancesMonthlySheet('Tidak Ada Data', collect());
        }

        return $sheets;
    }

    protected function buildServiceChecks(): array
    {
        $checks = [];

        foreach ($this->maintenances->groupBy('asset_id') as $items) {
            $checks += MaintenanceServiceCheck::forAssetMaintenances($items);
        }

        return $checks;
    }
}
