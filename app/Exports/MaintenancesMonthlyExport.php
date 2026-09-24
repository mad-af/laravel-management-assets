<?php

namespace App\Exports;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Exports\Sheets\MaintenancesMonthlySheet;
use App\Models\AssetMaintenance;
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

    /**
     * Compare each vehicle maintenance against the service target that was in effect
     * before it (the "next service" date/KM set by the previous completed maintenance
     * of the same asset), so the export shows whether the service was late.
     */
    protected function buildServiceChecks(): array
    {
        $checks = [];

        foreach ($this->maintenances->groupBy('asset_id') as $items) {
            $profile = $items->first()->asset?->vehicleProfile;
            if (! $profile) {
                continue;
            }

            $completed = $items
                ->filter(fn ($m) => $m->status === MaintenanceStatus::COMPLETED && $m->completed_at)
                ->sortBy('completed_at');

            foreach ($items as $m) {
                $referenceDate = $m->started_at ?? $m->completed_at ?? $m->created_at;
                $isCompleted = $m->status === MaintenanceStatus::COMPLETED;

                // Latest completed maintenance before this one that set a next-service target
                $previous = $completed
                    ->filter(fn ($p) => $p->id !== $m->id && $p->completed_at->lte($referenceDate))
                    ->filter(fn ($p) => $p->next_service_date || $p->next_service_target_odometer_km)
                    ->last();

                $targetDate = $previous?->next_service_date;
                $targetKm = $previous?->next_service_target_odometer_km;

                if (! $previous) {
                    // No history in the system: fall back to the target stored on the vehicle
                    $targetDate = $isCompleted ? $m->next_service_date_before : $profile->next_service_date;
                    $targetKm = $isCompleted ? null : $profile->service_target_odometer_km;
                }

                $actualDate = $m->started_at ?? $m->completed_at;
                $actualKm = $m->odometer_km_at_service
                    ?? ($isCompleted ? null : ($profile->current_odometer_km ?: null));

                $lateDays = null;
                $overKm = null;

                // Only scheduled (preventive) services are judged against the target
                if ($m->type === MaintenanceType::PREVENTIVE && $m->status !== MaintenanceStatus::CANCELLED) {
                    if ($targetDate && $actualDate && $actualDate->copy()->startOfDay()->gt($targetDate)) {
                        $lateDays = (int) $targetDate->copy()->startOfDay()->diffInDays($actualDate->copy()->startOfDay());
                    }

                    if ($targetKm && $actualKm && $actualKm > $targetKm) {
                        $overKm = $actualKm - $targetKm;
                    }
                }

                $checks[$m->id] = [
                    'target_date' => $targetDate,
                    'actual_date' => $actualDate,
                    'target_km' => $targetKm,
                    'actual_km' => $actualKm,
                    'late_days' => $lateDays,
                    'over_km' => $overKm,
                    'evaluated' => $m->type === MaintenanceType::PREVENTIVE
                        && $m->status !== MaintenanceStatus::CANCELLED
                        && ($targetDate || $targetKm),
                ];
            }
        }

        return $checks;
    }
}
