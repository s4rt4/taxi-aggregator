<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function checkout(Booking $booking, StripeService $stripeService)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);
        abort_unless($booking->payment_type === 'prepaid', 400);
        abort_if($booking->payment()->where('status', 'succeeded')->exists(), 400);

        try {
            $session = $stripeService->createCheckoutSession($booking);
            return redirect($session->url);
        } catch (ApiErrorException $e) {
            return back()->with('error', 'Payment setup failed. Please try again.');
        }
    }

    public function success(Request $request, Booking $booking, StripeService $stripeService)
    {
        if ($request->has('session_id')) {
            try {
                $stripeService->handlePaymentSuccess($request->session_id);
            } catch (\Exception $e) {
                // Webhook will handle it
            }
        }

        return view('payment.success', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        return view('payment.cancel', compact('booking'));
    }
}
