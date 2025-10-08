<?php

namespace App\Models;

use App\Enums\VehicleOdometerSource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Boot the model to handle vehicle profile updates.
     */
    protected static function booted()
    {
        // When odometer log is created, update current_odometer_km in VehicleProfile
        static::created(function ($odometerLog) {
            $asset = $odometerLog->asset;

            if ($asset && $asset->vehicleProfile) {
                // Only update if the new odometer reading is higher than current
                $currentOdometer = $asset->vehicleProfile->current_odometer_km ?? 0;

                if ($odometerLog->odometer_km > $currentOdometer) {
                    $asset->vehicleProfile()->update([
                        'current_odometer_km' => $odometerLog->odometer_km,
                    ]);
                }
            }
        });

        // When odometer log is updated, check if we need to update VehicleProfile
        static::updated(function ($odometerLog) {
            if ($odometerLog->wasChanged('odometer_km')) {
                $asset = $odometerLog->asset;

                if ($asset && $asset->vehicleProfile) {
                    // Get the highest odometer reading from all logs for this asset
                    $maxOdometer = static::where('asset_id', $odometerLog->asset_id)
                        ->max('odometer_km');

                    $asset->vehicleProfile()->update([
                        'current_odometer_km' => $maxOdometer,
                    ]);
                }
            }
        });

        // When odometer log is deleted, recalculate current_odometer_km
        static::deleted(function ($odometerLog) {
            $asset = $odometerLog->asset;

            if ($asset && $asset->vehicleProfile) {
                // Get the highest remaining odometer reading
                $maxOdometer = static::where('asset_id', $odometerLog->asset_id)
                    ->max('odometer_km') ?? 0;

                $asset->vehicleProfile()->update([
                    'current_odometer_km' => $maxOdometer,
                ]);
            }
        });
    }
}
