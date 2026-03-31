<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'passenger_id',
        'operator_id',
        'driver_id',
        'rating',
        'timing_rating',
        'fare_rating',
        'driver_rating',
        'vehicle_rating',
        'route_rating',
        'comment',
        'operator_reply',
        'operator_replied_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'timing_rating' => 'integer',
            'fare_rating' => 'integer',
            'driver_rating' => 'integer',
            'vehicle_rating' => 'integer',
            'route_rating' => 'integer',
            'operator_replied_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    // Relationships

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
