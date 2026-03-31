<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetGreetLocation extends Model
{
    use HasFactory;

    protected $table = 'meet_greet_locations';

    protected $fillable = [
        'name',
        'type',
        'code',
        'lat',
        'lng',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    // Relationships

    public function charges(): HasMany
    {
        return $this->hasMany(MeetGreetCharge::class);
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
