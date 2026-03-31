<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAvailability extends Model
{
    use HasFactory;

    protected $table = 'vehicle_availability';

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'day_of_week',
        'max_vehicles',
        'same_every_day',
    ];

    protected function casts(): array
    {
        return [
            'max_vehicles' => 'integer',
            'same_every_day' => 'boolean',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetType(): BelongsTo
    {
        return $this->belongsTo(FleetType::class);
    }
}
