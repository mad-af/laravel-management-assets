<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insurance extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    /**
     * Get the policies provided by this insurance.
     */
    public function vehicleInsurancePolicies(): HasMany
    {
        return $this->hasMany(VehicleInsurancePolicy::class);
    }
}
