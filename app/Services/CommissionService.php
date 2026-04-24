<?php

namespace App\Services;

use App\Models\Operator;

class CommissionService
{
    // Default tier commission rates
    // Base rate: 20% flat; higher tiers get loyalty discounts
    const TIER_RATES = [
        'basic' => 20.00,
        'airport_approved' => 18.00,
        'top_tier' => 15.00,
    ];

    // Default rate for new operators
    const DEFAULT_RATE = 20.00;

    /**
     * Get commission rate for an operator.
     * Uses custom rate if set, otherwise tier-based rate.
     */
    public static function getRate(Operator $operator): float
    {
        // If operator has a custom commission rate that differs from tier default, use it
        $tierRate = self::TIER_RATES[$operator->tier] ?? self::DEFAULT_RATE;

        // If admin has set a custom rate different from tier, use the custom rate
        // Otherwise auto-apply tier rate
        if ($operator->commission_rate != $tierRate && $operator->commission_rate > 0) {
            return (float) $operator->commission_rate;
        }

        return $tierRate;
    }

    /**
     * Calculate commission for a booking amount.
     */
    public static function calculate(Operator $operator, float $bookingTotal): array
    {
        $rate = self::getRate($operator);
        $commission = round($bookingTotal * ($rate / 100), 2);
        $operatorEarnings = round($bookingTotal - $commission, 2);

        return [
            'rate' => $rate,
            'commission' => $commission,
            'operator_earnings' => $operatorEarnings,
            'booking_total' => $bookingTotal,
        ];
    }

    /**
     * Auto-update commission rate when operator tier changes.
     */
    public static function updateRateForTier(Operator $operator): void
    {
        $tierRate = self::TIER_RATES[$operator->tier] ?? self::DEFAULT_RATE;
        $operator->update(['commission_rate' => $tierRate]);
    }
}
