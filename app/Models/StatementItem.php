<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_id',
        'booking_id',
        'payment_type',
        'fare_amount',
        'commission_amount',
        'fine_amount',
        'net_amount',
    ];

    protected function casts(): array
    {
        return [
            'fare_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'fine_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }

    // Relationships

    public function statement(): BelongsTo
    {
        return $this->belongsTo(Statement::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
