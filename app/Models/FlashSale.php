<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlashSale extends Model
{
    use HasFactory;

    protected $table = 'flash_sales';

    protected $fillable = [
        'operator_id',
        'starts_at',
        'ends_at',
        'discount_type',
        'discount_value',
        'all_fleet_types',
        'all_routes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'discount_value' => 'decimal:2',
            'all_fleet_types' => 'boolean',
            'all_routes' => 'boolean',
        ];
    }

    // Relationships

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function fleetTypes(): BelongsToMany
    {
        return $this->belongsToMany(FleetType::class, 'flash_sale_fleet_types')
            ->withTimestamps();
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }
}
