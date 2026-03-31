<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreePickupPostcode extends Model
{
    use HasFactory;

    protected $table = 'free_pickup_postcodes';

    protected $fillable = [
        'operator_id',
        'postcode_area',
    ];

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
