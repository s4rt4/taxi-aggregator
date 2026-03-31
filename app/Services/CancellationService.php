<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class CancellationService
{
    /**
     * UK standard cancellation tiers.
     */
    const POLICIES = [
        ['hours_before' => 48, 'refund_percent' => 100, 'label' => 'Full refund'],
        ['hours_before' => 24, 'refund_percent' => 75, 'label' => '75% refund'],
        ['hours_before' => 4, 'refund_percent' => 50, 'label' => '50% refund'],
        ['hours_before' => 2, 'refund_percent' => 25, 'label' => '25% refund'],
        ['hours_before' => 0, 'refund_percent' => 0, 'label' => 'No refund'],
    ];

    /**
     * Calculate refund amount for a booking based on time until pickup.
     */
    public static function getRefundAmount(Booking $booking): array
    {
        $hoursUntilPickup = now()->diffInHours(Carbon::parse($booking->pickup_datetime), false);

        // If pickup has passed, no refund
        if ($hoursUntilPickup < 0) {
            return [
                'refund_amount' => 0,
                'refund_percent' => 0,
                'policy' => 'Pickup time has passed - no refund',
                'hours_until_pickup' => $hoursUntilPickup,
            ];
        }

        foreach (self::POLICIES as $tier) {
            if ($hoursUntilPickup >= $tier['hours_before']) {
                $refundAmount = round($booking->total_price * ($tier['refund_percent'] / 100), 2);
                return [
                    'refund_amount' => $refundAmount,
                    'refund_percent' => $tier['refund_percent'],
                    'policy' => $tier['label'],
                    'hours_until_pickup' => $hoursUntilPickup,
                ];
            }
        }

        return [
            'refund_amount' => 0,
            'refund_percent' => 0,
            'policy' => 'No refund',
            'hours_until_pickup' => $hoursUntilPickup,
        ];
    }

    /**
     * Get human-readable cancellation policy text.
     */
    public static function getPolicyText(): array
    {
        return [
            'More than 48 hours before pickup: Full refund (100%)',
            '24-48 hours before pickup: 75% refund',
            '4-24 hours before pickup: 50% refund',
            '2-4 hours before pickup: 25% refund',
            'Less than 2 hours before pickup: No refund',
        ];
    }
}
