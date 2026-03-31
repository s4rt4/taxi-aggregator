<?php

namespace App\Http\Controllers;

use App\Listeners\SendBookingNotifications;
use App\Models\Booking;
use App\Models\Review;
use App\Notifications\NewReviewReceived;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function bookings(Request $request)
    {
        $bookings = auth()->user()->bookings()
            ->with(['operator', 'fleetType', 'review'])
            ->latest('pickup_datetime')
            ->paginate(10);

        return view('passenger.bookings', compact('bookings'));
    }

    public function bookingDetail(Booking $booking)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);

        $booking->load(['operator', 'fleetType', 'driver', 'payment', 'review']);

        return view('passenger.booking-detail', compact('booking'));
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);
        abort_unless(in_array($booking->status, ['pending', 'accepted']), 400);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => 'passenger',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        // Notify operator of cancellation
        SendBookingNotifications::onBookingCancelled($booking, 'passenger', $request->input('reason'));

        return redirect()->route('passenger.bookings')->with('success', 'Booking cancelled successfully.');
    }

    public function storeReview(Request $request, Booking $booking)
    {
        abort_unless($booking->passenger_id === auth()->id(), 403);
        abort_unless($booking->status === 'completed', 400);
        abort_if($booking->review()->exists(), 400);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'timing_rating' => 'nullable|integer|min:1|max:5',
            'fare_rating' => 'nullable|integer|min:1|max:5',
            'driver_rating' => 'nullable|integer|min:1|max:5',
            'vehicle_rating' => 'nullable|integer|min:1|max:5',
            'route_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'booking_id' => $booking->id,
            'passenger_id' => auth()->id(),
            'operator_id' => $booking->operator_id,
            'driver_id' => $booking->driver_id,
            ...$request->only([
                'rating',
                'timing_rating',
                'fare_rating',
                'driver_rating',
                'vehicle_rating',
                'route_rating',
                'comment',
            ]),
        ]);

        // Notify operator of new review
        $review->load('booking');
        $operatorUser = $booking->operator?->user;
        if ($operatorUser) {
            $operatorUser->notify(new NewReviewReceived($review));
        }

        return back()->with('success', 'Review submitted. Thank you!');
    }

    public function profile()
    {
        return view('passenger.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only('name', 'email', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }
}
