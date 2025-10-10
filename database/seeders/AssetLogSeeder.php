<?php

namespace Database\Seeders;

use App\Enums\AssetLogAction;
use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::all();
        $users = User::all();

        if ($assets->count() === 0 || $users->count() === 0) {
            return;
        }

        foreach ($assets->take(10) as $asset) {
            // Asset creation log
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $users->random()->id,
                'action' => AssetLogAction::CREATED,
                'changed_fields' => null,
                'notes' => 'Asset dibuat dalam sistem',
                'created_at' => $asset->created_at,
                'updated_at' => $asset->created_at,
            ]);

            // Random activity logs for each asset
            $randomActivities = [
                [
                    'action' => AssetLogAction::SCANNED,
                    'notes' => 'Asset di-scan untuk verifikasi lokasi',
                    'changed_fields' => null,
                ],
                [
                    'action' => AssetLogAction::UPDATED,
                    'notes' => 'Informasi asset diperbarui',
                    'changed_fields' => ['name' => ['old' => 'Old Name', 'new' => $asset->name]],
                ],
                [
                    'action' => AssetLogAction::CONDITION_CHANGED,
                    'notes' => 'Kondisi asset berubah setelah inspeksi',
                    'changed_fields' => ['condition' => ['old' => 'good', 'new' => $asset->condition->value]],
                ],
                [
                    'action' => AssetLogAction::STATUS_CHANGED,
                    'notes' => 'Status asset diperbarui',
                    'changed_fields' => ['status' => ['old' => 'available', 'new' => $asset->status->value]],
                ],
                [
                    'action' => AssetLogAction::LOCATION_CHANGED,
                    'notes' => 'Asset dipindahkan ke lokasi baru',
                    'changed_fields' => ['branch_id' => ['old' => 'old-branch-id', 'new' => $asset->branch_id]],
                ],
                [
                    'action' => AssetLogAction::MAINTENANCE_START,
                    'notes' => 'Maintenance rutin dimulai',
                    'changed_fields' => null,
                ],
                [
                    'action' => AssetLogAction::MAINTENANCE_END,
                    'notes' => 'Maintenance selesai, asset dalam kondisi baik',
                    'changed_fields' => null,
                ],
                [
                    'action' => AssetLogAction::CHECKED_OUT,
                    'notes' => 'Asset dipinjam oleh karyawan',
                    'changed_fields' => null,
                ],
                [
                    'action' => AssetLogAction::CHECKED_IN,
                    'notes' => 'Asset dikembalikan dalam kondisi baik',
                    'changed_fields' => null,
                ],
            ];

            // Add 2-5 random activities per asset
            $numberOfActivities = rand(2, 5);
            $selectedActivities = collect($randomActivities)->random($numberOfActivities);

            foreach ($selectedActivities as $index => $activity) {
                $logDate = $asset->created_at->addDays(rand(1, 30) + ($index * 10));

                AssetLog::create([
                    'asset_id' => $asset->id,
                    'user_id' => $users->random()->id,
                    'action' => $activity['action'],
                    'changed_fields' => $activity['changed_fields'],
                    'notes' => $activity['notes'],
                    'created_at' => $logDate,
                    'updated_at' => $logDate,
                ]);
            }
        }

        // Additional specific logs for demonstration
        $firstAsset = $assets->first();
        $adminUser = $users->where('email', 'admin@example.com')->first();

        if ($firstAsset && $adminUser) {
            AssetLog::create([
                'asset_id' => $firstAsset->id,
                'user_id' => $adminUser->id,
                'action' => AssetLogAction::REPAIRED,
                'changed_fields' => [
                    'condition' => ['old' => 'damaged', 'new' => 'good'],
                    'notes' => ['old' => 'Rusak pada bagian layar', 'new' => 'Sudah diperbaiki'],
                ],
                'notes' => 'Asset berhasil diperbaiki oleh teknisi',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            AssetLog::create([
                'asset_id' => $firstAsset->id,
                'user_id' => $adminUser->id,
                'action' => AssetLogAction::SCANNED,
                'changed_fields' => null,
                'notes' => 'Scan QR code untuk audit bulanan',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }
    }
}
