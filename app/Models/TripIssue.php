<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'operator_id',
        'issue_type',
        'description',
        'fine_amount',
        'fine_status',
        'investigation_status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'fine_amount' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
