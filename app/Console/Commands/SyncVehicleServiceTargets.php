<?php

namespace App\Console\Commands;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\AssetMaintenance;
use App\Models\VehicleProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncVehicleServiceTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:sync-vehicle-service
                            {--apply : Simpan perubahan (tanpa opsi ini hanya pratinjau)}
                            {--code=* : Hanya proses kode perawatan tertentu, misal --code=WO-202609-041}
                            {--except=* : Lewati kode perawatan tertentu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan tanggal/KM service berikutnya di kendaraan dari perawatan terakhir yang sudah selesai tetapi belum tersalin ke kendaraan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = (bool) $this->option('apply');
        $codes = array_filter((array) $this->option('code'));
        $except = array_filter((array) $this->option('except'));
        $hasBeforeColumn = Schema::hasColumn('asset_maintenances', 'next_service_date_before');

        if (! $hasBeforeColumn) {
            $this->warn('Kolom next_service_date_before belum ada. Jalankan "php artisan migrate --force" dulu agar target lama ikut tercatat.');
        }

        $rows = [];
        $fixed = 0;

        VehicleProfile::with('asset')->chunkById(100, function ($profiles) use ($apply, $codes, $except, $hasBeforeColumn, &$rows, &$fixed) {
            foreach ($profiles as $profile) {
                $latest = AssetMaintenance::query()
                    ->where('asset_id', $profile->asset_id)
                    ->where('status', MaintenanceStatus::COMPLETED->value)
                    ->whereNotNull('completed_at')
                    ->orderByDesc('completed_at')
                    ->first();

                if (! $latest
                    || ($codes && ! in_array($latest->code, $codes, true))
                    || in_array($latest->code, $except, true)) {
                    continue;
                }

                // Already applied: the vehicle's last service is on/after this completion
                if ($profile->last_service_date
                    && $profile->last_service_date->gte($latest->completed_at->copy()->startOfDay())) {
                    continue;
                }

                // Same rule as the AssetMaintenance updated hook: only overwrite filled values
                $updateData = ['last_service_date' => $latest->completed_at];
                if ($latest->next_service_date) {
                    $updateData['next_service_date'] = $latest->next_service_date;
                }
                if ($latest->next_service_target_odometer_km) {
                    $updateData['service_target_odometer_km'] = $latest->next_service_target_odometer_km;
                }

                $recordBefore = $hasBeforeColumn
                    && $latest->type === MaintenanceType::PREVENTIVE
                    && $latest->next_service_date
                    && ! $latest->next_service_date_before;

                $rows[] = [
                    $latest->code,
                    $profile->asset?->name ?? $profile->asset_id,
                    $this->change($profile->last_service_date?->format('d/m/Y'), $latest->completed_at->format('d/m/Y')),
                    $this->change($profile->next_service_date?->format('d/m/Y'), $latest->next_service_date?->format('d/m/Y')),
                    $this->change($profile->service_target_odometer_km, $latest->next_service_target_odometer_km)
                        .($latest->next_service_target_odometer_km && $latest->next_service_target_odometer_km < $profile->current_odometer_km ? ' ⚠' : ''),
                    $this->formatKm($profile->current_odometer_km),
                ];

                if (! $apply) {
                    continue;
                }

                DB::transaction(function () use ($profile, $latest, $updateData, $recordBefore) {
                    if ($recordBefore) {
                        // The profile still holds the target that was in effect before this service
                        $latest->next_service_date_before = $profile->next_service_date;
                        $latest->saveQuietly();
                    }

                    $profile->update($updateData);
                });

                $fixed++;
            }
        });

        if (empty($rows)) {
            $this->info('Semua kendaraan sudah sinkron. Tidak ada yang perlu dibenahi.');

            return self::SUCCESS;
        }

        $this->table(
            ['Kode Perawatan', 'Kendaraan', 'Service Terakhir', 'Service Berikutnya', 'Target KM', 'Odometer Saat Ini'],
            $rows
        );

        if (collect($rows)->contains(fn ($r) => str_ends_with($r[4], '⚠'))) {
            $this->warn('⚠ = target KM baru lebih kecil dari odometer saat ini. Cek dulu, mungkin salah ketik di form perawatan.');
        }

        if ($apply) {
            $this->info("{$fixed} kendaraan berhasil disinkronkan.");
        } else {
            $this->warn(count($rows).' kendaraan belum sinkron. Ini hanya pratinjau, jalankan ulang dengan --apply untuk menyimpan semua, atau --apply --code=KODE untuk kendaraan tertentu.');
        }

        return self::SUCCESS;
    }

    private function change($from, $to): string
    {
        $from = $from ?? '-';

        if ($to === null || (string) $from === (string) $to) {
            return (string) $from;
        }

        return "{$from} → {$to}";
    }

    private function formatKm(?int $km): string
    {
        return $km ? number_format($km, 0, ',', '.') : '-';
    }
}
