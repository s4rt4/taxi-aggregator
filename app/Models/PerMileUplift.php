<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerMileUplift extends Model
{
    use HasFactory;

    protected $table = 'per_mile_uplifts';

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'mile_from',
        'mile_to',
        'uplift_percentage',
    ];

    protected function casts(): array
    {
        return [
            'mile_from' => 'integer',
            'mile_to' => 'integer',
            'uplift_percentage' => 'decimal:2',
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
