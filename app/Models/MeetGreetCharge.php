<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetGreetCharge extends Model
{
    use HasFactory;

    protected $table = 'meet_greet_charges';

    protected $fillable = [
        'operator_id',
        'meet_greet_location_id',
        'charge',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'charge' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(MeetGreetLocation::class, 'meet_greet_location_id');
    }
}
