<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CorporateUser extends Pivot
{
    use HasFactory;

    protected $table = 'corporate_users';

    public $incrementing = true;

    protected $fillable = [
        'corporate_id',
        'user_id',
        'corporate_role',
        'employee_id',
        'cost_centre',
        'monthly_budget',
        'monthly_spent',
        'requires_approval',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_budget' => 'decimal:2',
            'monthly_spent' => 'decimal:2',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuperUser(): bool
    {
        return $this->corporate_role === 'super_user';
    }

    public function isUser(): bool
    {
        return $this->corporate_role === 'user';
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
