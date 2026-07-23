<?php

namespace App\Models;

use App\Casts\ServiceDetailsCast;
use App\Casts\ServiceTasksCast;
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
        'code',
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
        'next_service_date_before',
        'invoice_no',
        'service_tasks',
        'service_details',
    ];

    protected $casts = [
        'type' => MaintenanceType::class,
        'status' => MaintenanceStatus::class,
        'priority' => MaintenancePriority::class,
        'started_at' => 'datetime',
        'estimated_completed_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_service_date' => 'date',
        'next_service_date_before' => 'date',
        'odometer_km_at_service' => 'integer',
        'next_service_target_odometer_km' => 'integer',
        'service_tasks' => ServiceTasksCast::class,
        'service_details' => ServiceDetailsCast::class,
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

    public function isOverdue(): bool
    {
        if (! $this->estimated_completed_at) {
            return false;
        }

        if (! in_array($this->status, [MaintenanceStatus::OPEN, MaintenanceStatus::IN_PROGRESS])) {
            return false;
        }

        return now()->isAfter($this->estimated_completed_at);
    }

    public function getDaysOverdue(): ?int
    {
        if (! $this->estimated_completed_at) {
            return null;
        }

        if (! $this->isOverdue()) {
            return null;
        }

        return (int) $this->estimated_completed_at->startOfDay()->diffInDays(now()->startOfDay(), false);
    }

    /**
     * Boot the model to handle asset status updates.
     */
    protected static function booted()
    {
        // When maintenance is created, set asset status to maintenance and create odometer log
        static::creating(function ($maintenance) {
            // Auto-generate code if not provided
            if (empty($maintenance->code)) {
                $maintenance->code = generate_maintenance_code();
            }
        });

        static::created(function ($maintenance) {
            // Only set asset to maintenance if the maintenance status is not completed or cancelled
            if (! in_array($maintenance->status, [MaintenanceStatus::COMPLETED, MaintenanceStatus::CANCELLED])) {
                $asset = $maintenance->asset;
                if ($asset) {
                    $asset->status = AssetStatus::MAINTENANCE;
                    $asset->save();
                }
            }

            // DO NOT create odometer log here anymore
            // Odometer log should only be created when maintenance is completed
            // to avoid prematurely updating current_odometer_km based on user input during WO creation
        });

        // When maintenance status is updated, check if we need to update asset status
        static::updated(function ($maintenance) {
            if ($maintenance->wasChanged('status')) {
                $newStatus = $maintenance->status;

                // If maintenance is completed or cancelled, set asset back to active
                if (in_array($newStatus, [MaintenanceStatus::COMPLETED, MaintenanceStatus::CANCELLED])) {
                    $asset = $maintenance->asset;
                    if ($asset) {
                        $asset->status = AssetStatus::ACTIVE;
                        $asset->save();
                    }

                    // If maintenance is completed and has vehicle profile, update next service data
                    if ($newStatus === MaintenanceStatus::COMPLETED && $maintenance->asset->vehicleProfile) {
                        $updateData = [];

                        if ($maintenance->completed_at) {
                            $updateData['last_service_date'] = $maintenance->completed_at;
                        }

                        if ($maintenance->next_service_target_odometer_km) {
                            $updateData['service_target_odometer_km'] = $maintenance->next_service_target_odometer_km;
                        }

                        if ($maintenance->next_service_date) {
                            // For PREVENTIVE maintenance, save the old next_service_date before updating
                            if ($maintenance->type === MaintenanceType::PREVENTIVE) {
                                $maintenance->next_service_date_before = $maintenance->asset->vehicleProfile->next_service_date;
                                $maintenance->saveQuietly(['next_service_date_before']);
                            }

                            $updateData['next_service_date'] = $maintenance->next_service_date;
                        }

                        if (! empty($updateData)) {
                            $maintenance->asset->vehicleProfile()->update($updateData);
                        }
                    }
                }
                // If maintenance is reopened (back to open or in_progress), set asset to maintenance
                elseif (in_array($newStatus, [MaintenanceStatus::OPEN, MaintenanceStatus::IN_PROGRESS])) {
                    $asset = $maintenance->asset;
                    if ($asset) {
                        $asset->status = AssetStatus::MAINTENANCE;
                        $asset->save();
                    }
                }
            }

            if ($maintenance->wasChanged('odometer_km_at_service')) {
                // Create odometer log if maintenance has odometer data and asset is a vehicle
                if ($maintenance->odometer_km_at_service && $maintenance->asset->vehicleProfile) {
                    VehicleOdometerLog::create([
                        'asset_id' => $maintenance->asset_id,
                        'odometer_km' => $maintenance->odometer_km_at_service,
                        'read_at' => $maintenance->started_at ?? now(),
                        'source' => \App\Enums\VehicleOdometerSource::SERVICE,
                        'notes' => "Update Maintenance: {$maintenance->title}",
                    ]);
                }
            }
        });

        // When maintenance is deleted, set asset back to active
        static::deleted(function ($maintenance) {
            $asset = $maintenance->asset;
            if ($asset) {
                $asset->status = AssetStatus::ACTIVE;
                $asset->save();
            }
        });
    }
}
