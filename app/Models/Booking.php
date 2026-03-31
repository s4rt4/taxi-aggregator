<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'passenger_id',
        'operator_id',
        'fleet_type_id',
        'vehicle_id',
        'driver_id',
        'quote_id',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'destination_address',
        'destination_lat',
        'destination_lng',
        'waypoints',
        'distance_miles',
        'estimated_duration_minutes',
        'pickup_datetime',
        'is_return_journey',
        'return_datetime',
        'passenger_name',
        'passenger_phone',
        'passenger_email',
        'passenger_count',
        'luggage_count',
        'special_requirements',
        'flight_number',
        'train_number',
        'meet_and_greet',
        'meet_greet_charge',
        'price_source',
        'base_price',
        'commission_amount',
        'commission_rate',
        'surcharges',
        'discount_amount',
        'total_price',
        'currency',
        'payment_type',
        'status',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
        'accepted_at',
        'driver_assigned_at',
        'en_route_at',
        'arrived_at',
        'started_at',
        'completed_at',
        'operator_notes',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_lat' => 'decimal:7',
            'pickup_lng' => 'decimal:7',
            'destination_lat' => 'decimal:7',
            'destination_lng' => 'decimal:7',
            'waypoints' => 'array',
            'distance_miles' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
            'pickup_datetime' => 'datetime',
            'is_return_journey' => 'boolean',
            'return_datetime' => 'datetime',
            'passenger_count' => 'integer',
            'luggage_count' => 'integer',
            'meet_and_greet' => 'boolean',
            'meet_greet_charge' => 'decimal:2',
            'base_price' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'surcharges' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'accepted_at' => 'datetime',
            'driver_assigned_at' => 'datetime',
            'en_route_at' => 'datetime',
            'arrived_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Boot method for auto-generating reference

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->reference)) {
                $booking->reference = static::generateReference();
            }
        });
    }

    /**
     * Generate a unique booking reference in format TX-YYYYMMDD-XXXX.
     */
    public static function generateReference(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $reference = "TX-{$date}-{$random}";

        // Ensure uniqueness
        while (static::where('reference', $reference)->exists()) {
            $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $reference = "TX-{$date}-{$random}";
        }

        return $reference;
    }

    // Relationships

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetType(): BelongsTo
    {
        return $this->belongsTo(FleetType::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function tripIssues(): HasMany
    {
        return $this->hasMany(TripIssue::class);
    }
}
