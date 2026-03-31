<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Listeners\SendBookingNotifications;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $operator = auth()->user()->operator;

        if (!$operator) {
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $nextPickup = null;

            return view('operator.bookings.index', compact('bookings', 'nextPickup'));
        }

        $query = $operator->bookings()->with(['fleetType', 'driver'])->latest('pickup_datetime');

        // Search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', '%' . $search . '%')
                  ->orWhere('passenger_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pickup_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pickup_datetime', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10)->appends($request->query());

        // Find next upcoming pickup for the countdown widget
        $nextPickup = $operator->bookings()
            ->whereIn('status', ['accepted', 'driver_assigned'])
            ->where('pickup_datetime', '>=', now())
            ->orderBy('pickup_datetime')
            ->first();

        // Calculate countdown values for the view
        $countdownDays = 0;
        $countdownHours = 0;
        $countdownMinutes = 0;

        if ($nextPickup) {
            $diff = now()->diff($nextPickup->pickup_datetime);
            $countdownDays = $diff->days;
            $countdownHours = $diff->h;
            $countdownMinutes = $diff->i;
        }

        return view('operator.bookings.index', compact(
            'bookings',
            'nextPickup',
            'countdownDays',
            'countdownHours',
            'countdownMinutes'
        ));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Authorize: booking must belong to the authenticated user's operator
        $operator = auth()->user()->operator;

        if (!$operator || $booking->operator_id !== $operator->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,driver_assigned,en_route,arrived,in_progress,completed,no_show'],
        ]);

        // Define valid status transitions
        $validTransitions = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['driver_assigned', 'cancelled'],
            'driver_assigned' => ['en_route', 'cancelled'],
            'en_route' => ['arrived'],
            'arrived' => ['in_progress', 'no_show'],
            'in_progress' => ['completed'],
        ];

        $currentStatus = $booking->status;
        $newStatus = $validated['status'];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return redirect()->back()->with('error', "Cannot transition from '{$currentStatus}' to '{$newStatus}'.");
        }

        // Update the status and its corresponding timestamp
        $timestampField = match ($newStatus) {
            'accepted' => 'accepted_at',
            'driver_assigned' => 'driver_assigned_at',
            'en_route' => 'en_route_at',
            'arrived' => 'arrived_at',
            'in_progress' => 'started_at',
            'completed' => 'completed_at',
            default => null,
        };

        $updateData = ['status' => $newStatus];

        if ($timestampField) {
            $updateData[$timestampField] = now();
        }

        $booking->update($updateData);

        // Notify passenger of status change
        SendBookingNotifications::onBookingStatusUpdated($booking, $newStatus);

        return redirect()->back()->with('success', 'Booking status updated to ' . str_replace('_', ' ', $newStatus) . '.');
    }
}
