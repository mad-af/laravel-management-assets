<?php

namespace App\Support;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\AssetMaintenance;
use App\Models\VehicleProfile;
use Illuminate\Support\Collection;

/**
 * Compares a vehicle maintenance against the service target that was in effect
 * before it (the "next service" date/KM set by the previous completed maintenance
 * of the same asset), to tell whether the service was late.
 *
 * Shared by the maintenance export and the kanban card so both agree.
 */
class MaintenanceServiceCheck
{
    public const EMPTY = [
        'target_date' => null,
        'actual_date' => null,
        'target_km' => null,
        'actual_km' => null,
        'late_days' => null,
        'over_km' => null,
        'evaluated' => false,
    ];

    /**
     * Evaluate one maintenance, looking up its previous completed maintenance.
     */
    public static function for(AssetMaintenance $m): array
    {
        $profile = $m->asset?->vehicleProfile;
        if (! $profile) {
            return self::EMPTY;
        }

        $previous = AssetMaintenance::query()
            ->where('asset_id', $m->asset_id)
            ->where('id', '!=', $m->id)
            ->where('status', MaintenanceStatus::COMPLETED->value)
            ->whereNotNull('completed_at')
            ->where('completed_at', '<=', self::referenceDate($m))
            ->where(fn ($q) => $q->whereNotNull('next_service_date')->orWhereNotNull('next_service_target_odometer_km'))
            ->orderByDesc('completed_at')
            ->first();

        return self::evaluate($m, $previous, $profile);
    }

    /**
     * Evaluate all maintenances of one asset in memory (no extra queries).
     *
     * @return array<string, array> keyed by maintenance id
     */
    public static function forAssetMaintenances(Collection $items): array
    {
        $profile = $items->first()?->asset?->vehicleProfile;
        if (! $profile) {
            return [];
        }

        $completed = $items
            ->filter(fn ($m) => $m->status === MaintenanceStatus::COMPLETED && $m->completed_at)
            ->filter(fn ($m) => $m->next_service_date || $m->next_service_target_odometer_km)
            ->sortBy('completed_at');

        $checks = [];
        foreach ($items as $m) {
            $referenceDate = self::referenceDate($m);
            $previous = $completed
                ->filter(fn ($p) => $p->id !== $m->id && $p->completed_at->lte($referenceDate))
                ->last();

            $checks[$m->id] = self::evaluate($m, $previous, $profile);
        }

        return $checks;
    }

    public static function evaluate(AssetMaintenance $m, ?AssetMaintenance $previous, VehicleProfile $profile): array
    {
        $isCompleted = $m->status === MaintenanceStatus::COMPLETED;

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

        // Only scheduled (preventive) services are judged against the target
        $evaluated = $m->type === MaintenanceType::PREVENTIVE
            && $m->status !== MaintenanceStatus::CANCELLED
            && ($targetDate || $targetKm);

        $lateDays = null;
        $overKm = null;

        if ($evaluated) {
            if ($targetDate && $actualDate && $actualDate->copy()->startOfDay()->gt($targetDate)) {
                $lateDays = (int) $targetDate->copy()->startOfDay()->diffInDays($actualDate->copy()->startOfDay());
            }

            if ($targetKm && $actualKm && $actualKm > $targetKm) {
                $overKm = $actualKm - $targetKm;
            }
        }

        return [
            'target_date' => $targetDate,
            'actual_date' => $actualDate,
            'target_km' => $targetKm,
            'actual_km' => $actualKm,
            'late_days' => $lateDays,
            'over_km' => $overKm,
            'evaluated' => $evaluated,
        ];
    }

    public static function isLate(array $check): bool
    {
        return $check['evaluated'] && ($check['late_days'] || $check['over_km']);
    }

    /**
     * Human-readable status, e.g. "Terlambat 10 hari, Lewat 1.200 KM".
     */
    public static function statusText(array $check): string
    {
        if (! $check['evaluated']) {
            return '-';
        }

        $issues = [];
        if ($check['late_days']) {
            $issues[] = 'Terlambat '.$check['late_days'].' hari';
        }
        if ($check['over_km']) {
            $issues[] = 'Lewat '.number_format($check['over_km'], 0, ',', '.').' KM';
        }

        return $issues ? implode(', ', $issues) : 'Tepat Waktu';
    }

    private static function referenceDate(AssetMaintenance $m)
    {
        return $m->started_at ?? $m->completed_at ?? $m->created_at;
    }
}
