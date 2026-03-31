<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorWeeklyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'week_start',
        'week_end',
        'total_pickups',
        'rejected_trips',
        'driver_no_shows',
        'lost_from_no_shows',
        'late_trips',
        'failed_meet_greets',
        'total_fines',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'total_pickups' => 'integer',
            'rejected_trips' => 'integer',
            'driver_no_shows' => 'integer',
            'lost_from_no_shows' => 'decimal:2',
            'late_trips' => 'integer',
            'failed_meet_greets' => 'integer',
            'total_fines' => 'decimal:2',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
