<?php

namespace App\Models;

use App\Enums\InsuranceClaimIncidentType;
use App\Enums\InsuranceClaimSource;
use App\Enums\InsuranceClaimStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceClaim extends Model
{
    use HasUuids;

    protected $fillable = [
        'policy_id',
        'asset_id',
        'claim_no',
        'incident_date',
        'incident_type',
        'incident_other',
        'description',
        'source',
        'asset_maintenance_id',
        'status',
        'claim_documents',
        'amount_approved',
        'amount_paid',
        'created_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'incident_type' => InsuranceClaimIncidentType::class,
        'source' => InsuranceClaimSource::class,
        'status' => InsuranceClaimStatus::class,
        'claim_documents' => 'array',
        'amount_approved' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(AssetMaintenance::class, 'asset_maintenance_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
