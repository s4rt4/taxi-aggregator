<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FleetType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_passengers',
        'max_passengers',
        'fuel_category',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function perMilePrices()
    {
        return $this->hasMany(PerMilePrice::class);
    }

    public function locationPrices()
    {
        return $this->hasMany(LocationPrice::class);
    }

    public function postcodeAreaPrices()
    {
        return $this->hasMany(PostcodeAreaPrice::class);
    }

    public function noticePeriods()
    {
        return $this->hasMany(NoticePeriod::class);
    }

    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class);
    }

    public function vehicleAvailability()
    {
        return $this->hasMany(VehicleAvailability::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
