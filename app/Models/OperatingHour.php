<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'is_24_hours',
        'start_time',
        'end_time',
        'excluded_days',
    ];

    protected function casts(): array
    {
        return [
            'is_24_hours' => 'boolean',
            'excluded_days' => 'array',
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
