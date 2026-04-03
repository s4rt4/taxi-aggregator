<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Notifications\OperatorApproved;
use App\Notifications\OperatorRejected;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $query = Operator::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('operator_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%");
            });
        }

        $operators = $query->latest()->paginate(20)->withQueryString();

        return view('admin.operators.index', compact('operators'));
    }

    public function pending()
    {
        $operators = Operator::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.operators.pending', compact('operators'));
    }

    public function show(Operator $operator)
    {
        $operator->load([
            'user',
            'contacts',
            'bookings' => fn ($q) => $q->latest()->limit(10),
            'bookings.fleetType',
            'reviews' => fn ($q) => $q->latest()->limit(10),
            'reviews.passenger',
            'drivers',
            'vehicles',
        ]);

        return view('admin.operators.show', compact('operator'));
    }

    public function approve(Operator $operator)
    {
        $operator->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Notify operator user of approval
        if ($operator->user) {
            $operator->user->notify(new OperatorApproved($operator));
        }

        return back()->with('success', "Operator '{$operator->operator_name}' has been approved.");
    }

    public function reject(Request $request, Operator $operator)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $operator->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify operator user of rejection
        if ($operator->user) {
            $operator->user->notify(new OperatorRejected($operator, $request->rejection_reason));
        }

        return back()->with('success', "Operator '{$operator->operator_name}' has been rejected.");
    }

    public function suspend(Operator $operator)
    {
        $operator->update(['status' => 'suspended']);

        return back()->with('success', "Operator '{$operator->operator_name}' has been suspended.");
    }

    public function reactivate(Operator $operator)
    {
        $operator->update(['status' => 'approved']);

        return back()->with('success', "Operator '{$operator->operator_name}' has been reactivated.");
    }

    public function updateTier(Request $request, Operator $operator)
    {
        $request->validate([
            'tier' => 'required|in:basic,airport_approved,top_tier',
        ]);

        $operator->update(['tier' => $request->tier]);

        // Auto-update commission rate to match the new tier
        CommissionService::updateRateForTier($operator);

        return back()->with('success', "Operator tier updated to '{$request->tier}'.");
    }

    public function updateCommission(Request $request, Operator $operator)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:50',
        ]);

        $operator->update(['commission_rate' => $request->commission_rate]);

        return back()->with('success', "Commission rate updated to {$request->commission_rate}%.");
    }
}
