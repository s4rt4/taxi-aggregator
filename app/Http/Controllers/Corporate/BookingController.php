<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected function requireCorporate()
    {
        $user = auth()->user();
        $corporate = $user->activeCorporate();

        if (!$corporate || $corporate->status !== 'approved') {
            abort(403, 'Your corporate account is not yet active.');
        }

        return $corporate;
    }

    public function index(Request $request)
    {
        $corporate = $this->requireCorporate();
        $user = auth()->user();
        $isSuperUser = $user->isCorporateSuperUser();

        $query = Booking::where('corporate_id', $corporate->id)
            ->with(['passenger', 'operator', 'fleetType']);

        // Non-super users only see their own bookings
        if (!$isSuperUser) {
            $query->where('passenger_id', $user->id);
        }

        if ($request->filled('employee_id')) {
            $query->where('passenger_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('pickup_datetime', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('pickup_datetime', '<=', $request->to_date);
        }

        $bookings = $query->latest()->paginate(25);

        $employees = $corporate->users()->get();

        return view('corporate.bookings.index', compact('corporate', 'bookings', 'employees', 'isSuperUser'));
    }

    public function show(Booking $booking)
    {
        $corporate = $this->requireCorporate();
        $user = auth()->user();

        abort_unless($booking->corporate_id === $corporate->id, 403);

        if (!$user->isCorporateSuperUser() && $booking->passenger_id !== $user->id) {
            abort(403);
        }

        $booking->load(['passenger', 'operator', 'fleetType', 'driver', 'vehicle', 'corporateInvoice']);

        return view('corporate.bookings.show', compact('booking', 'corporate'));
    }

    public function approve(Booking $booking)
    {
        $corporate = $this->requireCorporate();
        $user = auth()->user();

        abort_unless($user->isCorporateSuperUser(), 403);
        abort_unless($booking->corporate_id === $corporate->id, 403);
        abort_unless($booking->approval_status === 'pending', 422);

        $booking->update([
            'approval_status' => 'approved',
            'status' => 'pending',
        ]);

        // Now that it's approved, notify operator
        \App\Listeners\SendBookingNotifications::onBookingCreated($booking);

        // Dispatch to iCabbi if operator has it enabled
        \App\Services\Dispatch\DispatchManager::dispatchBooking($booking);

        return back()->with('success', 'Booking approved and sent to operator.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $corporate = $this->requireCorporate();
        $user = auth()->user();

        abort_unless($user->isCorporateSuperUser(), 403);
        abort_unless($booking->corporate_id === $corporate->id, 403);
        abort_unless($booking->approval_status === 'pending', 422);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'approval_status' => 'rejected',
            'status' => 'cancelled',
            'cancelled_by' => 'passenger',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['reason'] ?? 'Rejected by corporate super user',
        ]);

        // Release budget if it was reserved
        if ($corporate->monthly_budget !== null) {
            $corporate->deductSpending((float) $booking->total_price);
        }

        $corporateUser = \App\Models\CorporateUser::where('corporate_id', $corporate->id)
            ->where('user_id', $booking->passenger_id)
            ->first();
        if ($corporateUser && $corporateUser->monthly_budget !== null) {
            $corporateUser->deductSpending((float) $booking->total_price);
        }

        return back()->with('success', 'Booking rejected.');
    }
}
