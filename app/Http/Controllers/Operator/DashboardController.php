<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $operator = auth()->user()->operator;

        // Redirect new operators to onboarding if they haven't completed setup
        if (!$operator || !$operator->operator_name || !$operator->licence_number) {
            return redirect()->route('operator.onboarding');
        }

        $stats = [
            'total_jobs' => $operator?->bookings()->count() ?? 0,
            'completed' => $operator?->bookings()->where('status', 'completed')->count() ?? 0,
            'rating' => $operator?->rating_avg ?? 0,
            'earnings' => $operator?->bookings()->where('status', 'completed')->sum('total_price') ?? 0,
        ];

        $upcomingBookings = $operator?->bookings()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('pickup_datetime', '>=', now())
            ->orderBy('pickup_datetime')
            ->limit(5)
            ->get() ?? collect();

        return view('dashboard.operator', compact('stats', 'upcomingBookings'));
    }
}
