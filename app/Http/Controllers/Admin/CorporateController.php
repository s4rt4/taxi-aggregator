<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corporate;
use Illuminate\Http\Request;

class CorporateController extends Controller
{
    public function index(Request $request)
    {
        $query = Corporate::withCount(['corporateUsers', 'bookings', 'invoices']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        $corporates = $query->latest()->paginate(25);

        return view('admin.corporates.index', compact('corporates'));
    }

    public function pending()
    {
        $corporates = Corporate::pending()
            ->withCount('corporateUsers')
            ->latest()
            ->paginate(25);

        return view('admin.corporates.pending', compact('corporates'));
    }

    public function show(Corporate $corporate)
    {
        $corporate->load(['corporateUsers.user', 'invoices' => fn ($q) => $q->latest()->limit(10)]);
        $recentBookings = $corporate->bookings()->with(['passenger', 'operator'])->latest()->limit(20)->get();

        return view('admin.corporates.show', compact('corporate', 'recentBookings'));
    }

    public function approve(Request $request, Corporate $corporate)
    {
        abort_unless($corporate->status === 'pending', 422);

        $corporate->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', "{$corporate->company_name} approved successfully.");
    }

    public function reject(Request $request, Corporate $corporate)
    {
        abort_unless($corporate->status === 'pending', 422);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $corporate->update([
            'status' => 'closed',
            'notes' => $validated['reason'] ?? 'Rejected by admin',
        ]);

        return back()->with('success', "{$corporate->company_name} rejected.");
    }

    public function suspend(Request $request, Corporate $corporate)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $corporate->update([
            'status' => 'suspended',
            'notes' => $validated['reason'] ?? 'Suspended by admin',
        ]);

        return back()->with('success', "{$corporate->company_name} suspended.");
    }

    public function reactivate(Corporate $corporate)
    {
        $corporate->update(['status' => 'approved']);

        return back()->with('success', "{$corporate->company_name} reactivated.");
    }
}
