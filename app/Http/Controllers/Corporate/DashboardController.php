<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $corporate = $user->activeCorporate();

        // Not yet onboarded - redirect
        if (!$corporate) {
            return redirect()->route('corporate.onboarding');
        }

        // Pending approval
        if ($corporate->status !== 'approved') {
            return redirect()->route('corporate.onboarding.complete');
        }

        $corporateUser = $user->corporateProfile();

        // Stats
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $bookingsThisMonth = Booking::where('corporate_id', $corporate->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $spentThisMonth = Booking::where('corporate_id', $corporate->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_price');

        $activeEmployees = $corporate->corporateUsers()->where('is_active', true)->count();

        $pendingInvoices = $corporate->invoices()->whereIn('status', ['sent', 'overdue'])->count();

        $pendingApprovals = Booking::where('corporate_id', $corporate->id)
            ->where('approval_status', 'pending')
            ->count();

        $recentBookings = Booking::where('corporate_id', $corporate->id)
            ->with(['passenger', 'operator', 'fleetType'])
            ->latest()
            ->limit(10)
            ->get();

        $remainingBudget = $corporate->remainingBudget();
        $budgetPercent = $corporate->budgetPercentUsed();

        return view('corporate.dashboard', compact(
            'corporate',
            'corporateUser',
            'bookingsThisMonth',
            'spentThisMonth',
            'activeEmployees',
            'pendingInvoices',
            'pendingApprovals',
            'recentBookings',
            'remainingBudget',
            'budgetPercent'
        ));
    }
}
