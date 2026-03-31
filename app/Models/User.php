<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'avatar',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role helpers
    public function isPassenger(): bool
    {
        return $this->role === 'passenger';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'operator' => 'operator.dashboard',
            'driver' => 'driver.dashboard',
            default => 'dashboard',
        };
    }

    // Relationships
    public function operator()
    {
        return $this->hasOne(\App\Models\Operator::class);
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'passenger_id');
    }
}
