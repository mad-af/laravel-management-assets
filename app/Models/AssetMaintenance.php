<?php

namespace App\Models;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_id',
        'title',
        'type',
        'status',
        'priority',
        'started_at',
        'estimated_completed_at',
        'completed_at',
        'cost',
        'technician_name',
        'vendor_name',
        'notes',
        'odometer_km_at_service',
        'next_service_target_odometer_km',
        'next_service_date',
        'invoice_no',
    ];

    protected $casts = [
        'type' => MaintenanceType::class,
        'status' => MaintenanceStatus::class,
        'priority' => MaintenancePriority::class,
        'started_at' => 'datetime',
        'estimated_completed_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_service_date' => 'date',
        'odometer_km_at_service' => 'integer',
        'next_service_target_odometer_km' => 'integer',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_name', 'name');
    }
}
