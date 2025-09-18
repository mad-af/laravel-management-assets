<?php

namespace Database\Seeders;

use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferPriority;
use App\Enums\AssetTransferType;
use App\Enums\AssetTransferItemStatus;
use App\Enums\AssetLocationChangeType;
use App\Models\AssetTransfer;
use App\Models\AssetTransferItem;
use App\Models\AssetLocationHistory;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample data
        $company = Company::first();
        $user = User::first();
        $assets = Asset::limit(3)->get();
        $locations = Location::limit(2)->get();

        if (!$company || !$user || $assets->count() < 2 || $locations->count() < 2) {
            $this->command->info('Insufficient data for seeding. Please ensure you have companies, users, assets, and locations.');
            return;
        }

        // Create sample asset transfers
        $transfers = [
            [
                'transfer_no' => 'TRF-' . date('Y') . '-001',
                'type' => AssetTransferType::LOCATION,
                'status' => 'draft',
                'priority' => AssetTransferPriority::NORMAL,
                'reason' => 'Reorganisasi kantor',
                'notes' => 'Transfer aset untuk reorganisasi tata letak kantor baru',
            ],
            [
                'transfer_no' => 'TRF-' . date('Y') . '-002',
                'type' => AssetTransferType::MAINTENANCE,
                'status' => 'executed',
                'priority' => AssetTransferPriority::HIGH,
                'reason' => 'Maintenance rutin',
                'notes' => 'Transfer aset ke workshop untuk maintenance berkala',
            ],
            [
                'transfer_no' => 'TRF-' . date('Y') . '-003',
                'type' => AssetTransferType::DEPARTMENT,
                'status' => 'submitted',
                'priority' => AssetTransferPriority::LOW,
                'reason' => 'Kebutuhan departemen IT',
                'notes' => 'Transfer komputer ke departemen IT untuk upgrade sistem',
            ],
        ];

        foreach ($transfers as $index => $transferData) {
            $transfer = AssetTransfer::create([
                'id' => Str::uuid(),
                'company_id' => $company->id,
                'transfer_no' => $transferData['transfer_no'],
                'type' => $transferData['type'],
                'status' => $transferData['status'],
                'priority' => $transferData['priority'],
                'reason' => $transferData['reason'],
                'notes' => $transferData['notes'],
                'requested_by' => $user->id,
                'requested_at' => now()->subDays(rand(1, 30)),
                'scheduled_at' => now()->addDays(rand(1, 7)),
            ]);

            // Create transfer items
            $asset = $assets->get($index % $assets->count());
            $fromLocation = $locations->first();
            $toLocation = $locations->last();

            $transferItem = AssetTransferItem::create([
                'id' => Str::uuid(),
                'asset_transfer_id' => $transfer->id,
                'asset_id' => $asset->id,
                'from_location_id' => $fromLocation->id,
                'to_location_id' => $toLocation->id,
                'transferred_at' => $transferData['status'] === AssetTransferStatus::EXECUTED ? now() : null,
            ]);

            // Create location history if executed
            if ($transferData['status'] === 'executed') {
                AssetLocationHistory::create([
                    'id' => Str::uuid(),
                    'asset_id' => $asset->id,
                    'from_location_id' => $fromLocation->id,
                    'to_location_id' => $toLocation->id,
                    'changed_at' => now(),
                    'changed_by' => $user->id,
                    'transfer_id' => $transfer->id,
                    'change_type' => AssetLocationChangeType::TRANSFER,
                    'remark' => 'Asset transferred via ' . $transfer->transfer_no,
                ]);
            }
        }

        $this->command->info('Asset transfer sample data created successfully!');
    }
}
