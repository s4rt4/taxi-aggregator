<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostcodeArea extends Model
{
    use HasFactory;

    protected $table = 'postcode_areas';

    protected $fillable = [
        'code',
        'area_name',
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

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
