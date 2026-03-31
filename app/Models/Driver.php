<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'operator_id',
        'first_name',
        'last_name',
        'licence_number',
        'mobile_number',
        'vehicle_make',
        'vehicle_model',
        'vehicle_max_passengers',
        'registration_plate',
        'dbs_status',
        'dbs_expiry',
        'photo',
        'is_active',
        'is_available',
        'current_lat',
        'current_lng',
        'location_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'dbs_expiry' => 'date',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'current_lat' => 'decimal:7',
            'current_lng' => 'decimal:7',
            'location_updated_at' => 'datetime',
        ];
    }

    // Accessors

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
