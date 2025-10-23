<?php

namespace App\Models;

use App\Enums\InsuranceStatus;
use App\Enums\InsurancePolicyType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsurancePolicy extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_id',
        'insurance_id',
        'policy_no',
        'policy_type',
        'start_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'policy_type' => InsurancePolicyType::class,
        'status' => InsuranceStatus::class,
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class, 'policy_id');
    }
}
