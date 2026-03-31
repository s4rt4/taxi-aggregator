<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'card_brand',
        'card_last4',
        'refund_amount',
        'refund_reason',
        'stripe_refund_id',
        'metadata',
        'failure_reason',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'metadata' => 'array',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    // Relationships

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
