<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleStripe(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $payment = Payment::where('stripe_checkout_session_id', $session->id)->first();
                if ($payment && $payment->status !== 'succeeded') {
                    $payment->update([
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'status' => 'succeeded',
                        'paid_at' => now(),
                    ]);
                    $payment->booking->update(['status' => 'accepted']);
                }
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                Payment::where('stripe_payment_intent_id', $intent->id)->update([
                    'status' => 'failed',
                    'failure_reason' => $intent->last_payment_error?->message,
                ]);
                break;
        }

        return response('OK', 200);
    }
}
