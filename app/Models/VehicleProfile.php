<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleProfile extends Model
{
    use HasUuids;

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_id',
        'year_purchase',
        'year_manufacture',
        'current_odometer_km',
        'last_service_date',
        'service_target_odometer_km',
        'next_service_date',
        'plate_no',
        'vin',
    ];

    protected $casts = [
        'asset_id' => 'string',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'current_odometer_km' => 'integer',
        'service_target_odometer_km' => 'integer',
        'year_purchase' => 'integer',
        'year_manufacture' => 'integer',
    ];

    /**
     * Get the asset that owns the vehicle profile.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the vehicle tax types for the vehicle profile through asset.
     */
    public function vehicleTaxTypes(): HasMany
    {
        return $this->hasMany(VehicleTaxType::class, 'asset_id', 'asset_id');
    }

    /**
     * Get the vehicle tax histories for the vehicle profile through asset.
     */
    public function vehicleTaxHistories(): HasMany
    {
        return $this->hasMany(VehicleTaxHistory::class, 'asset_id', 'asset_id');
    }
}
