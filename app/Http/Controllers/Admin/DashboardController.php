<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'operators' => Operator::count(),
            'passengers' => User::where('role', 'passenger')->count(),
            'bookings' => Booking::count(),
            'revenue' => Booking::where('status', 'completed')->sum('commission_amount'),
        ];

        $pendingOperators = Operator::where('status', 'pending')->latest()->limit(5)->get();
        $recentBookings = Booking::with(['operator', 'fleetType'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingOperators', 'recentBookings'));
    }
}
