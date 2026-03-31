<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'pickup_range_miles',
        'dropoff_range_miles',
    ];

    protected function casts(): array
    {
        return [
            'pickup_range_miles' => 'integer',
            'dropoff_range_miles' => 'integer',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
