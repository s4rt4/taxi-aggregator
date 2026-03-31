<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationPrice extends Model
{
    use HasFactory;

    protected $table = 'location_prices';

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'start_postcode',
        'start_radius_miles',
        'finish_postcode',
        'finish_radius_miles',
        'price',
        'also_reverse',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_radius_miles' => 'integer',
            'finish_radius_miles' => 'integer',
            'price' => 'decimal:2',
            'also_reverse' => 'boolean',
            'is_active' => 'boolean',
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
