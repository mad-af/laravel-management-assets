<?php

namespace App\Models;

use App\Enums\AssetLocationChangeType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLocationHistory extends Model
{
    use HasUuids;

    protected $table = 'asset_location_history';
    
    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'from_location_id',
        'to_location_id',
        'changed_at',
        'changed_by',
        'transfer_id',
        'change_type',
        'remark',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'change_type' => AssetLocationChangeType::class,
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(AssetTransfer::class, 'transfer_id');
    }
}
