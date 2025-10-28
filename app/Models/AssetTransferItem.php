<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetTransferItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_transfer_id',
        'asset_id',
        'from_branch_id',
        'to_branch_id',
    ];

    protected $casts = [
        //
    ];

    public function assetTransfer(): BelongsTo
    {
        return $this->belongsTo(AssetTransfer::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Add event so when item is created, set asset status to IN_TRANSFER
    protected static function booted()
    {
        static::created(function (self $item) {
            if ($item->asset_id) {
                $asset = \App\Models\Asset::find($item->asset_id);
                if ($asset) {
                    $asset->status = AssetStatus::IN_TRANSFER;
                    $asset->save();
                }
            }
        });
    }
}
