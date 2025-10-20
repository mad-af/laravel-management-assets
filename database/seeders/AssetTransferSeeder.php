<?php

namespace Database\Seeders;

use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferType;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\AssetTransferItem;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada minimal 2 cabang dan beberapa asset
        $branches = Branch::where('is_active', true)->inRandomOrder()->take(2)->get();
        if ($branches->count() < 2) {
            $this->command?->warn('AssetTransferSeeder: Butuh minimal 2 cabang aktif. Seeder dilewati.');
            return;
        }

        [$fromBranch, $toBranch] = [$branches[0], $branches[1]];

        $assets = Asset::where('branch_id', $fromBranch->id)->inRandomOrder()->take(2)->get();
        if ($assets->isEmpty()) {
            $this->command?->warn('AssetTransferSeeder: Tidak ada asset di cabang asal. Seeder dilewati.');
            return;
        }

        $deliveryUser = User::first();
        if (! $deliveryUser) {
            $this->command?->warn('AssetTransferSeeder: Tidak ada user untuk delivery_by. Seeder dilewati.');
            return;
        }

        // Buat satu transfer sederhana berstatus shipped
        $transfer = new AssetTransfer([
            'company_id' => $fromBranch->company_id,
            'transfer_no' => 'TRF-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4)),
            'reason' => 'Seeder demo transfer aset',
            'status' => AssetTransferStatus::SHIPPED,
            'type' => AssetTransferType::BRANCH,
            'delivery_by' => $deliveryUser->id,
            'delivery_at' => now(),
            'notes' => 'Dibuat oleh AssetTransferSeeder',
        ]);

        // Set cabang asal/tujuan secara langsung (kolom ada di DB, fillable mungkin belum)
        $transfer->from_branch_id = $fromBranch->id;
        $transfer->to_branch_id = $toBranch->id;
        $transfer->save();

        // Tambahkan beberapa item dari cabang asal ke cabang tujuan
        foreach ($assets as $asset) {
            $item = new AssetTransferItem([
                'asset_transfer_id' => $transfer->id,
                'asset_id' => $asset->id,
            ]);
            $item->from_branch_id = $fromBranch->id;
            $item->to_branch_id = $toBranch->id;
            $item->save();
        }

        $this->command?->info("AssetTransferSeeder: Transfer {$transfer->transfer_no} dengan " . $assets->count() . ' item berhasil dibuat.');
    }
}