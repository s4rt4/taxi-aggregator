<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerMilePriceRange extends Model
{
    use HasFactory;

    protected $table = 'per_mile_price_ranges';

    protected $fillable = [
        'per_mile_price_id',
        'mile_from',
        'mile_to',
        'rate_per_mile',
    ];

    protected function casts(): array
    {
        return [
            'mile_from' => 'integer',
            'mile_to' => 'integer',
            'rate_per_mile' => 'decimal:2',
        ];
    }

    // Relationships

    public function perMilePrice(): BelongsTo
    {
        return $this->belongsTo(PerMilePrice::class);
    }
}
