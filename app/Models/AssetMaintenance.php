<?php

namespace App\Models;

use App\Enums\AssetStatus;
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
        'employee_id',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_name', 'name');
    }

    /**
     * Boot the model to handle asset status updates.
     */
    protected static function boot()
    {
        parent::boot();

        // When maintenance is created, set asset status to maintenance
        static::created(function ($maintenance) {
            $maintenance->asset()->update([
                'status' => AssetStatus::MAINTENANCE,
            ]);
        });

        // When maintenance status is updated, check if we need to update asset status
        static::updated(function ($maintenance) {
            if ($maintenance->wasChanged('status')) {
                $newStatus = $maintenance->status;

                // If maintenance is completed or cancelled, set asset back to active
                if (in_array($newStatus, [MaintenanceStatus::COMPLETED, MaintenanceStatus::CANCELLED])) {
                    $maintenance->asset()->update([
                        'status' => AssetStatus::ACTIVE,
                    ]);
                }
                // If maintenance is reopened (back to open or in_progress), set asset to maintenance
                elseif (in_array($newStatus, [MaintenanceStatus::OPEN, MaintenanceStatus::IN_PROGRESS])) {
                    $maintenance->asset()->update([
                        'status' => AssetStatus::MAINTENANCE,
                    ]);
                }
            }
        });

        // When maintenance is deleted, set asset back to active
        static::deleted(function ($maintenance) {
            $maintenance->asset()->update([
                'status' => AssetStatus::ACTIVE,
            ]);
        });
    }
}
