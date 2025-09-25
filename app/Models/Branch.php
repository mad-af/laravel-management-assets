<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function assetBranchHistories(): HasMany
    {
        return $this->hasMany(AssetBranchHistory::class, 'to_branch_id');
    }

    public function fromBranchHistories(): HasMany
    {
        return $this->hasMany(AssetBranchHistory::class, 'from_branch_id');
    }

    public function assetTransfersFrom(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'from_branch_id');
    }

    public function assetTransfersTo(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'to_branch_id');
    }
}
