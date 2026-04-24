<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['operator', 'fleetType', 'passenger']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->filled('date_from')) {
            $query->where('pickup_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('pickup_datetime', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->boolean('reallocated_only')) {
            $query->where('is_reallocated', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('passenger_email', 'like', "%{$search}%")
                  ->orWhere('pickup_address', 'like', "%{$search}%")
                  ->orWhere('destination_address', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'operator',
            'fleetType',
            'passenger',
            'driver',
            'vehicle',
            'payment',
            'review',
            'tripIssues',
            'originalOperator',
            'allocatedBy',
        ]);

        $approvedOperators = \App\Models\Operator::where('status', 'approved')
            ->orderBy('operator_name')
            ->get();

        return view('admin.bookings.show', compact('booking', 'approvedOperators'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,accepted,driver_assigned,en_route,arrived,in_progress,completed,cancelled,no_show',
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', "Booking status updated to '{$request->status}'.");
    }

    public function addNote(Request $request, Booking $booking)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $existing = $booking->admin_notes ?? '';
        $timestamp = now()->format('d/m/Y H:i');
        $author = auth()->user()->name;
        $newNote = "[{$timestamp} - {$author}] {$request->admin_notes}";

        $booking->update([
            'admin_notes' => $existing ? "{$existing}\n{$newNote}" : $newNote,
        ]);

        return back()->with('success', 'Admin note added.');
    }

    public function allocate(Request $request, Booking $booking)
    {
        $request->validate([
            'operator_id' => ['required', 'exists:operators,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $newOperator = \App\Models\Operator::findOrFail($request->operator_id);

        // Only allow reallocation of pending/accepted bookings
        if (!in_array($booking->status, ['pending', 'accepted'])) {
            return back()->with('error', 'Only pending or accepted bookings can be reallocated.');
        }

        // Must be approved operator
        if ($newOperator->status !== 'approved') {
            return back()->with('error', 'Target operator must be approved.');
        }

        // If this is first reallocation, record the original operator
        if (!$booking->is_reallocated) {
            $booking->original_operator_id = $booking->operator_id;
        }

        $previousOperatorId = $booking->original_operator_id ?? $booking->operator_id;

        $booking->update([
            'operator_id' => $newOperator->id,
            'is_reallocated' => true,
            'allocated_by' => auth()->id(),
            'allocated_at' => now(),
            'allocation_reason' => $request->reason,
            'status' => 'pending', // reset to pending for new operator to accept
            'operator_notes' => trim(($booking->operator_notes ?? '') . "\n[Admin Allocation] Reassigned from operator #{$previousOperatorId} to #{$newOperator->id} by admin " . auth()->user()->name . " at " . now()->format('d/m/Y H:i') . ($request->reason ? " | Reason: {$request->reason}" : '')),
        ]);

        // Send notification to new operator (if notification class exists - optional)
        // NewBookingReceived can be dispatched to the new operator's user
        if ($newOperator->user) {
            $newOperator->user->notify(new \App\Notifications\NewBookingReceived($booking));
        }

        return back()->with('success', "Booking {$booking->reference} reallocated to {$newOperator->operator_name}.");
    }
}
