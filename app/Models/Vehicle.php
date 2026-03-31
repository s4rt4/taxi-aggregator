<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'make',
        'model',
        'colour',
        'year',
        'registration_plate',
        'max_passengers',
        'max_luggage',
        'wheelchair_accessible',
        'child_seat_available',
        'photo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'wheelchair_accessible' => 'boolean',
            'child_seat_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relationships

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }
}
