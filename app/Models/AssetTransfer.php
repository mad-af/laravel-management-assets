<?php

namespace App\Models;

use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferPriority;
use App\Enums\AssetTransferType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetTransfer extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
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
        'priority' => AssetTransferPriority::class,
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

    public function locationHistories(): HasMany
    {
        return $this->hasMany(AssetLocationHistory::class, 'transfer_id');
    }
}
