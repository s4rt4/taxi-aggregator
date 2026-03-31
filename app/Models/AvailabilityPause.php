<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityPause extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'pause_type',
        'duration_minutes',
        'starts_at',
        'ends_at',
        'all_fleet_types',
        'fleet_type_ids',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'all_fleet_types' => 'boolean',
            'fleet_type_ids' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
