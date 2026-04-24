<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Corporate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'legal_name',
        'business_type',
        'registration_number',
        'vat_number',
        'billing_email',
        'billing_phone',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_postcode',
        'billing_county',
        'monthly_budget',
        'monthly_spent',
        'invoicing_frequency',
        'payment_terms_days',
        'status',
        'approved_at',
        'approved_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_budget' => 'decimal:2',
            'monthly_spent' => 'decimal:2',
            'payment_terms_days' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'corporate_users')
            ->using(CorporateUser::class)
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

    public function corporateUsers(): HasMany
    {
        return $this->hasMany(CorporateUser::class);
    }

    public function superUsers(): HasMany
    {
        return $this->hasMany(CorporateUser::class)->where('corporate_role', 'super_user');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CorporateInvoice::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helpers

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function remainingBudget(): ?float
    {
        if ($this->monthly_budget === null) {
            return null;
        }

        return max(0, (float) $this->monthly_budget - (float) $this->monthly_spent);
    }

    public function hasBudget(float $amount): bool
    {
        if ($this->monthly_budget === null) {
            return true;
        }

        return (float) $this->monthly_spent + $amount <= (float) $this->monthly_budget;
    }

    public function budgetPercentUsed(): float
    {
        if ($this->monthly_budget === null || $this->monthly_budget == 0) {
            return 0;
        }

        return min(100, ((float) $this->monthly_spent / (float) $this->monthly_budget) * 100);
    }

    public function addSpending(float $amount): void
    {
        $this->increment('monthly_spent', $amount);
    }

    public function deductSpending(float $amount): void
    {
        $this->decrement('monthly_spent', $amount);
    }

    public function resetMonthlySpending(): void
    {
        $this->update(['monthly_spent' => 0]);
    }
}
