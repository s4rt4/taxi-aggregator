<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostcodeLeadTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'postcode_area',
        'notice_type',
        'notice_value',
        'fleet_type_ids',
    ];

    protected function casts(): array
    {
        return [
            'notice_value' => 'integer',
            'fleet_type_ids' => 'array',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
