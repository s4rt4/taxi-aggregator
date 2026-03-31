<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeadLegDiscount extends Model
{
    use HasFactory;

    protected $table = 'dead_leg_discounts';

    protected $fillable = [
        'operator_id',
        'from_area',
        'to_area',
        'available_from',
        'available_until',
        'discount_type',
        'discount_value',
        'status',
        'original_booking_id',
        'dld_booking_id',
    ];

    protected function casts(): array
    {
        return [
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'discount_value' => 'decimal:2',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function originalBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'original_booking_id');
    }

    public function dldBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'dld_booking_id');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('available_from', '<=', now())
            ->where('available_until', '>=', now());
    }
}
