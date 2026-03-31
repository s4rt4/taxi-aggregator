<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerMilePrice extends Model
{
    use HasFactory;

    protected $table = 'per_mile_prices';

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'rate_per_mile',
        'minimum_fare',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate_per_mile' => 'decimal:2',
            'minimum_fare' => 'decimal:2',
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

    public function ranges(): HasMany
    {
        return $this->hasMany(PerMilePriceRange::class);
    }
}
