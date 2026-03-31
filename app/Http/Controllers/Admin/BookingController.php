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
        ]);

        return view('admin.bookings.show', compact('booking'));
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
}
