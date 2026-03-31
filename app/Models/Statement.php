<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Statement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'operator_id',
        'period_start',
        'period_end',
        'gross_fares',
        'commission_deducted',
        'fines_deducted',
        'adjustments',
        'net_amount',
        'currency',
        'prepaid_booking_count',
        'cash_booking_count',
        'stripe_transfer_id',
        'stripe_payout_id',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_fares' => 'decimal:2',
            'commission_deducted' => 'decimal:2',
            'fines_deducted' => 'decimal:2',
            'adjustments' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'prepaid_booking_count' => 'integer',
            'cash_booking_count' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StatementItem::class);
    }
}
