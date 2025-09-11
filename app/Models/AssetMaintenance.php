<?php

namespace App\Models;

use App\Enums\MaintenanceType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenancePriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'status',
        'priority',
        'title',
        'description',
        'cost',
        'scheduled_date',
        'completed_date',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'type' => MaintenanceType::class,
        'status' => MaintenanceStatus::class,
        'priority' => MaintenancePriority::class,
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
