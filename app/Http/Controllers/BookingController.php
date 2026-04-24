<?php

namespace App\Http\Controllers;

use App\Listeners\SendBookingNotifications;
use App\Models\Booking;
use App\Models\Quote;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Quote $quote)
    {
        // Ensure quote hasn't expired
        if ($quote->expires_at && $quote->expires_at->isPast()) {
            return redirect()->route('home')->with('error', 'This quote has expired. Please search again.');
        }

        // Ensure quote hasn't already been booked
        if ($quote->booking()->exists()) {
            return redirect()->route('home')->with('error', 'This quote has already been booked.');
        }

        $quote->load(['operator', 'fleetType', 'quoteSearch']);

        return view('booking.create', compact('quote'));
    }

    public function store(Request $request, Quote $quote)
    {
        // Ensure quote hasn't expired
        if ($quote->expires_at && $quote->expires_at->isPast()) {
            return redirect()->route('home')->with('error', 'This quote has expired. Please search again.');
        }

        // Ensure quote hasn't already been booked
        if ($quote->booking()->exists()) {
            return redirect()->route('home')->with('error', 'This quote has already been booked.');
        }

        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_email' => 'nullable|email',
            'special_requirements' => 'nullable|string|max:1000',
            'flight_number' => 'nullable|string|max:20',
            'cost_centre' => 'nullable|string|max:100',
            'terms_accepted' => 'accepted',
        ]);

        $quote->load('quoteSearch');

        $user = auth()->user();
        $totalPrice = (float) $quote->total_price;

        // Check if this is a corporate booking
        $corporateUser = $user->corporateProfile();
        $corporate = $corporateUser?->corporate;
        $isCorporate = $corporate && $corporate->status === 'approved' && $corporateUser->is_active;

        if ($isCorporate) {
            // Budget checks
            if (!$corporate->hasBudget($totalPrice)) {
                return back()->with('error', 'This booking would exceed your company monthly budget.')->withInput();
            }

            if ($corporateUser->monthly_budget !== null && !$corporateUser->hasBudget($totalPrice)) {
                return back()->with('error', 'This booking would exceed your personal monthly budget.')->withInput();
            }
        }

        $commissionCalc = CommissionService::calculate($quote->operator, $quote->total_price);
        $commissionRate = $commissionCalc['rate'];
        $commissionAmount = $commissionCalc['commission'];

        $bookingData = [
            'passenger_id' => $user->id,
            'operator_id' => $quote->operator_id,
            'fleet_type_id' => $quote->fleet_type_id,
            'quote_id' => $quote->id,
            'pickup_address' => $quote->quoteSearch->pickup_address,
            'pickup_lat' => $quote->quoteSearch->pickup_lat,
            'pickup_lng' => $quote->quoteSearch->pickup_lng,
            'destination_address' => $quote->quoteSearch->destination_address,
            'destination_lat' => $quote->quoteSearch->destination_lat,
            'destination_lng' => $quote->quoteSearch->destination_lng,
            'distance_miles' => $quote->quoteSearch->distance_miles,
            'estimated_duration_minutes' => $quote->estimated_duration_minutes,
            'pickup_datetime' => $quote->quoteSearch->pickup_datetime,
            'passenger_name' => $request->passenger_name,
            'passenger_phone' => $request->passenger_phone,
            'passenger_email' => $request->passenger_email ?? $user->email,
            'passenger_count' => $quote->quoteSearch->passenger_count,
            'luggage_count' => $quote->quoteSearch->luggage_count,
            'special_requirements' => $request->special_requirements,
            'flight_number' => $request->flight_number,
            'meet_and_greet' => $quote->meet_and_greet,
            'meet_greet_charge' => $quote->meet_greet_charge,
            'price_source' => $quote->price_source,
            'base_price' => $quote->base_price,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'surcharges' => $quote->surcharges,
            'discount_amount' => $quote->flash_sale_discount + $quote->dead_leg_discount,
            'total_price' => $quote->total_price,
            'status' => 'pending',
        ];

        if ($isCorporate) {
            $bookingData['corporate_id'] = $corporate->id;
            $bookingData['cost_centre'] = $request->cost_centre ?? $corporateUser->cost_centre;
            $bookingData['payment_type'] = 'account';

            // If approval required, mark pending approval
            if ($corporateUser->requires_approval) {
                $bookingData['approval_status'] = 'pending';
                $bookingData['status'] = 'pending_approval';
            } else {
                $bookingData['approval_status'] = 'not_required';
            }
        } else {
            $bookingData['payment_type'] = 'prepaid';
            $bookingData['approval_status'] = 'not_required';
        }

        $booking = Booking::create($bookingData);

        if ($isCorporate) {
            // Reserve budget immediately
            $corporate->addSpending($totalPrice);
            if ($corporateUser->monthly_budget !== null) {
                $corporateUser->addSpending($totalPrice);
            }

            // Only dispatch notifications/iCabbi if no approval required
            if ($booking->approval_status !== 'pending') {
                SendBookingNotifications::onBookingCreated($booking);
                \App\Services\Dispatch\DispatchManager::dispatchBooking($booking);
            }

            return redirect()->route('booking.confirmation', $booking);
        }

        // Dispatch notifications to passenger and operator
        SendBookingNotifications::onBookingCreated($booking);

        // Dispatch to iCabbi if operator has it enabled
        $dispatchResult = \App\Services\Dispatch\DispatchManager::dispatchBooking($booking);

        return redirect()->route('booking.confirmation', $booking);
    }

    public function confirmation(Booking $booking)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);

        $booking->load(['operator', 'fleetType']);

        return view('booking.confirmation', compact('booking'));
    }
}
