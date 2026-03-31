<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'booking_id',
        'raised_by',
        'raised_by_role',
        'type',
        'description',
        'status',
        'resolution',
        'resolution_notes',
        'refund_amount',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class);
    }
}
