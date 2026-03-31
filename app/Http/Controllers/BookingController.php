<?php

namespace App\Http\Controllers;

use App\Listeners\SendBookingNotifications;
use App\Models\Booking;
use App\Models\Quote;
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
            'terms_accepted' => 'accepted',
        ]);

        $quote->load('quoteSearch');

        $commissionRate = $quote->operator->commission_rate ?? 12.00;
        $commissionAmount = round($quote->total_price * ($commissionRate / 100), 2);

        $booking = Booking::create([
            'passenger_id' => auth()->id(),
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
            'passenger_email' => $request->passenger_email ?? auth()->user()->email,
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
            'payment_type' => 'prepaid',
            'status' => 'pending',
        ]);

        // Dispatch notifications to passenger and operator
        SendBookingNotifications::onBookingCreated($booking);

        return redirect()->route('booking.confirmation', $booking);
    }

    public function confirmation(Booking $booking)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);

        $booking->load(['operator', 'fleetType']);

        return view('booking.confirmation', compact('booking'));
    }
}
