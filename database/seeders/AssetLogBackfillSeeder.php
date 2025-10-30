<?php

namespace Database\Seeders;

use App\Enums\AssetLogAction;
use App\Enums\AssetStatus;
use App\Models\AssetLog;
use Illuminate\Database\Seeder;

class AssetLogBackfillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updatedCount = 0;

        foreach (AssetLog::query()->whereNotNull('changed_fields')->cursor() as $log) {
            $fields = $log->changed_fields;
            if (! is_array($fields) || ! isset($fields['status'])) {
                continue;
            }

            $old = $fields['status']['old'] ?? null;
            $new = $fields['status']['new'] ?? null;

            $oldVal = $this->normalizeStatus($old);
            $newVal = $this->normalizeStatus($new);

            if (! $oldVal || ! $newVal || $oldVal === $newVal) {
                continue;
            }

            $message = null;
            $newAction = null;

            // Maintenance start/end
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::MAINTENANCE->value) {
                $message = 'Aset ini kini masuk masa perawatan';
                $newAction = AssetLogAction::MAINTENANCE_START;
            } elseif ($oldVal === AssetStatus::MAINTENANCE->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset selesai perawatan dan siap dipakai kembali';
                $newAction = AssetLogAction::MAINTENANCE_END;
            }

            // Loan start/end
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::ON_LOAN->value) {
                $message = 'Aset sedang dipinjamkan';
                $newAction = AssetLogAction::CHECKED_OUT;
            } elseif ($oldVal === AssetStatus::ON_LOAN->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset dikembalikan dan tersedia kembali';
                $newAction = AssetLogAction::CHECKED_IN;
            }

            // Transfer start/end (gunakan STATUS_CHANGED karena tidak ada enum khusus transfer)
            if ($oldVal === AssetStatus::ACTIVE->value && $newVal === AssetStatus::IN_TRANSFER->value) {
                $message = 'Aset sedang dalam proses pemindahan';
                $newAction = AssetLogAction::STATUS_CHANGED;
            } elseif ($oldVal === AssetStatus::IN_TRANSFER->value && $newVal === AssetStatus::ACTIVE->value) {
                $message = 'Aset telah diterima dan aktif kembali';
                $newAction = AssetLogAction::STATUS_CHANGED;
            }

            $dirty = false;
            if ($message && $log->notes !== $message) {
                $log->notes = $message;
                $dirty = true;
            }
            if ($newAction && $log->action !== $newAction) {
                $log->action = $newAction;
                $dirty = true;
            }

            if ($dirty) {
                $log->save();
                $updatedCount++;
            }
        }

        $this->command?->info("AssetLog backfill selesai. Log yang diperbarui: {$updatedCount}");
    }

    private function normalizeStatus($value): ?string
    {
        if ($value instanceof AssetStatus) {
            return $value->value;
        }
        if (is_string($value)) {
            return $value;
        }
        return null;
    }

    private function labelFor(string $value): string
    {
        try {
            return AssetStatus::from($value)->label();
        } catch (\ValueError $e) {
            return $value; // fallback jika bukan enum yang valid
        }
    }
}