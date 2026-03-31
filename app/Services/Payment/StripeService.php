<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for a booking.
     */
    public function createCheckoutSession(Booking $booking): StripeSession
    {
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($booking->currency),
                    'product_data' => [
                        'name' => "Taxi Booking {$booking->reference}",
                        'description' => "{$booking->pickup_address} to {$booking->destination_address}",
                    ],
                    'unit_amount' => (int) ($booking->total_price * 100), // pence
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', $booking) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', $booking),
            'metadata' => [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->reference,
            ],
        ]);

        // Create payment record
        Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->passenger_id,
            'stripe_checkout_session_id' => $session->id,
            'amount' => $booking->total_price,
            'currency' => $booking->currency,
            'status' => 'pending',
        ]);

        return $session;
    }

    /**
     * Handle successful payment (called from webhook or success redirect).
     */
    public function handlePaymentSuccess(string $sessionId): Payment
    {
        $session = StripeSession::retrieve($sessionId);
        $payment = Payment::where('stripe_checkout_session_id', $sessionId)->firstOrFail();

        $payment->update([
            'stripe_payment_intent_id' => $session->payment_intent,
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        // Update booking status
        $payment->booking->update(['status' => 'accepted']);

        return $payment;
    }

    /**
     * Process a refund.
     */
    public function refund(Payment $payment, ?float $amount = null): Payment
    {
        $refundParams = [
            'payment_intent' => $payment->stripe_payment_intent_id,
        ];

        if ($amount) {
            $refundParams['amount'] = (int) ($amount * 100);
        }

        $refund = Refund::create($refundParams);

        $refundAmount = $amount ?? $payment->amount;
        $newStatus = ($refundAmount >= $payment->amount) ? 'refunded' : 'partially_refunded';

        $payment->update([
            'stripe_refund_id' => $refund->id,
            'refund_amount' => $payment->refund_amount + $refundAmount,
            'status' => $newStatus,
            'refunded_at' => now(),
        ]);

        return $payment;
    }
}
