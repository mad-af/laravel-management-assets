<?php

namespace App\Models;

use App\Enums\VehicleOdometerSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VehicleOdometerLog extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'asset_id',
        'odometer_km',
        'read_at',
        'source',
        'notes',
    ];
    
    protected $casts = [
        'asset_id' => 'string',
        'odometer_km' => 'integer',
        'read_at' => 'datetime',
        'source' => VehicleOdometerSource::class,
    ];
    
    /**
     * Get the asset that owns the odometer log.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
