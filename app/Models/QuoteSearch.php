<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'destination_address',
        'destination_lat',
        'destination_lng',
        'pickup_datetime',
        'passenger_count',
        'luggage_count',
        'distance_miles',
        'estimated_duration_minutes',
        'is_return',
        'return_datetime',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'pickup_lat' => 'decimal:7',
            'pickup_lng' => 'decimal:7',
            'destination_lat' => 'decimal:7',
            'destination_lng' => 'decimal:7',
            'pickup_datetime' => 'datetime',
            'passenger_count' => 'integer',
            'luggage_count' => 'integer',
            'distance_miles' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
            'is_return' => 'boolean',
            'return_datetime' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
