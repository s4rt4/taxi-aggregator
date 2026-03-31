<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'fleet_type_id',
        'hours_notice',
    ];

    protected function casts(): array
    {
        return [
            'hours_notice' => 'integer',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetType(): BelongsTo
    {
        return $this->belongsTo(FleetType::class);
    }
}
