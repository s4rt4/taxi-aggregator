<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_search_id',
        'operator_id',
        'fleet_type_id',
        'price_source',
        'base_price',
        'meet_greet_charge',
        'flash_sale_discount',
        'dead_leg_discount',
        'surcharges',
        'total_price',
        'currency',
        'max_passengers',
        'max_luggage',
        'fleet_type_name',
        'operator_name',
        'operator_rating',
        'estimated_duration_minutes',
        'meet_and_greet',
        'flash_sale_id',
        'dead_leg_discount_id',
        'is_available',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'meet_greet_charge' => 'decimal:2',
            'flash_sale_discount' => 'decimal:2',
            'dead_leg_discount' => 'decimal:2',
            'surcharges' => 'decimal:2',
            'total_price' => 'decimal:2',
            'max_passengers' => 'integer',
            'max_luggage' => 'integer',
            'operator_rating' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
            'meet_and_greet' => 'boolean',
            'is_available' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    // Relationships

    public function quoteSearch(): BelongsTo
    {
        return $this->belongsTo(QuoteSearch::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetType(): BelongsTo
    {
        return $this->belongsTo(FleetType::class);
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
