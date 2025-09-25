<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetBranchHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'asset_branch_history';

    protected $fillable = [
        'asset_id',
        'from_branch_id',
        'to_branch_id',
        'transfer_id',
        'remark',
    ];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(AssetTransfer::class, 'transfer_id');
    }
}
