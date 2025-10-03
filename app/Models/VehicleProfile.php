<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'annual_tax_due_date',
        'plate_no',
        'vin',
    ];

    protected $casts = [
        'asset_id' => 'string',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'annual_tax_due_date' => 'date',
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
}
