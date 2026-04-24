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
        'admin_role_id',
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

    public function isCorporate(): bool
    {
        return $this->role === 'corporate';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'operator' => 'operator.dashboard',
            'driver' => 'driver.dashboard',
            'corporate' => 'corporate.dashboard',
            default => 'dashboard',
        };
    }

    // Admin role helpers
    public function adminRole()
    {
        return $this->belongsTo(\App\Models\AdminRole::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && $this->adminRole?->slug === 'super-admin';
    }

    public function hasAdminPermission(string $permission): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }

        if (!$this->adminRole) {
            return false;
        }

        return $this->adminRole->hasPermission($permission);
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

    public function corporates()
    {
        return $this->belongsToMany(\App\Models\Corporate::class, 'corporate_users')
            ->using(\App\Models\CorporateUser::class)
            ->withPivot([
                'corporate_role',
                'employee_id',
                'cost_centre',
                'monthly_budget',
                'monthly_spent',
                'requires_approval',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function corporateUsers()
    {
        return $this->hasMany(\App\Models\CorporateUser::class);
    }

    /**
     * Get the user's active corporate user record.
     */
    public function corporateProfile(): ?\App\Models\CorporateUser
    {
        return $this->corporateUsers()
            ->where('is_active', true)
            ->whereHas('corporate', fn ($q) => $q->where('status', 'approved'))
            ->latest()
            ->first();
    }

    /**
     * Get the user's active corporate company.
     */
    public function activeCorporate(): ?\App\Models\Corporate
    {
        $profile = $this->corporateProfile();
        return $profile?->corporate;
    }

    public function isCorporateUser(): bool
    {
        if (!$this->isCorporate()) {
            return false;
        }

        return $this->corporateUsers()->where('is_active', true)->exists();
    }

    public function isCorporateSuperUser(): bool
    {
        if (!$this->isCorporate()) {
            return false;
        }

        return $this->corporateUsers()
            ->where('is_active', true)
            ->where('corporate_role', 'super_user')
            ->exists();
    }
}
