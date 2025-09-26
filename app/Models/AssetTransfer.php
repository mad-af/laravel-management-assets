<?php

namespace App\Models;

use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetTransfer extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'from_location_id',
        'to_location_id',
        'transfer_no',
        'reason',
        'status',
        'requested_by',
        'approved_by',
        'scheduled_at',
        'executed_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'status' => AssetTransferStatus::class,
        'type' => AssetTransferType::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetTransferItem::class);
    }

}
