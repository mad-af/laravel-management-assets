<?php

namespace App\Models;

use App\Enums\VehicleTaxTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleTaxType extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_id',
        'tax_type',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'tax_type' => VehicleTaxTypeEnum::class,
    ];

    /**
     * Get the asset that owns the vehicle tax type.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the vehicle tax histories for the vehicle tax type.
     */
    public function vehicleTaxHistories(): HasMany
    {
        return $this->hasMany(VehicleTaxHistory::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (VehicleTaxType $vehicleTaxType) {
            if ($vehicleTaxType->due_date) {
                // Use the dedicated method from VehicleTaxHistory to create history record
                VehicleTaxHistory::createFromTaxType($vehicleTaxType);
            }
        });

        static::updated(function (VehicleTaxType $vehicleTaxType) {
            if ($vehicleTaxType->isDirty('due_date')) {
                $oldDue = $vehicleTaxType->getOriginal('due_date');
                $newDue = $vehicleTaxType->due_date;

                if ($oldDue) {
                    $query = VehicleTaxHistory::query()
                        ->where('vehicle_tax_type_id', $vehicleTaxType->id)
                        ->whereDate('due_date', $oldDue);

                    if ($newDue) {
                        $query->update([
                            'due_date' => $newDue,
                            'year' => $newDue->year,
                        ]);
                    } else {
                        $query->delete();
                    }
                }
            }
        });

        static::deleted(function (VehicleTaxType $vehicleTaxType) {
            if ($vehicleTaxType->due_date) {
                VehicleTaxHistory::query()
                    ->where('vehicle_tax_type_id', $vehicleTaxType->id)
                    ->whereDate('due_date', $vehicleTaxType->due_date)
                    ->delete();
            }
        });
    }
}
