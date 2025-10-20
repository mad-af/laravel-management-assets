<?php

namespace App\Models;

use App\Enums\AssetTransferAction;
use App\Enums\AssetTransferStatus;
use App\Enums\AssetTransferType;
use App\Support\SessionKey;
use Illuminate\Database\Eloquent\Builder;
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
        'delivery_by',
        'accepted_by',
        'accepted_at',
        'delivery_at',
        'notes',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'delivery_at' => 'datetime',
        'status' => AssetTransferStatus::class,
        'type' => AssetTransferType::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetTransferItem::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    // Determine current user's action (delivery or confirmation) based on session branch
    public function getActionAttribute(): ?AssetTransferAction
    {
        $currentBranchId = session_get(SessionKey::BranchId);
        if (! $currentBranchId) {
            return null;
        }

        // Support either location or branch field names
        $fromId = $this->from_location_id ?? $this->from_branch_id ?? null;
        $toId = $this->to_location_id ?? $this->to_branch_id ?? null;

        if ($fromId && (string) $fromId === (string) $currentBranchId) {
            return AssetTransferAction::DELIVERY;
        }

        if ($toId && (string) $toId === (string) $currentBranchId) {
            return AssetTransferAction::CONFIRMATION;
        }

        return null;
    }

    // Filter transfers where current session branch is the sender (delivery)
    public function scopeDeliveryAction(Builder $query, ?string $branchId = null)
    {
        $branchId = $branchId ?? session_get(SessionKey::BranchId);
        if (! $branchId) {
            return $query;
        }

        return $query->where(function ($q) use ($branchId) {
            $q->where('from_branch_id', $branchId)
                ->orWhere('from_location_id', $branchId);
        });
    }

    // Filter transfers where current session branch is the receiver (confirmation)
    public function scopeConfirmationAction(Builder $query, ?string $branchId = null)
    {
        $branchId = $branchId ?? session_get(SessionKey::BranchId);
        if (! $branchId) {
            return $query;
        }

        return $query->where(function ($q) use ($branchId) {
            $q->where('to_branch_id', $branchId)
                ->orWhere('to_location_id', $branchId);
        });
    }
}
