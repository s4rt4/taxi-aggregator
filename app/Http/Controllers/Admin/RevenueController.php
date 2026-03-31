<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');

        $dateFrom = match ($period) {
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'this_year' => Carbon::now()->startOfYear(),
            'all_time' => null,
            default => Carbon::now()->startOfMonth(),
        };

        $query = Booking::where('status', 'completed');
        if ($dateFrom) {
            $query->where('completed_at', '>=', $dateFrom);
        }

        $stats = [
            'total_revenue' => (clone $query)->sum('total_price'),
            'commission_earned' => (clone $query)->sum('commission_amount'),
            'total_bookings' => (clone $query)->count(),
            'avg_booking_value' => (clone $query)->avg('total_price') ?? 0,
        ];

        // Revenue by operator
        $operatorRevenue = Operator::select('operators.*')
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as total_revenue')
            ->selectRaw('COALESCE(SUM(bookings.commission_amount), 0) as total_commission')
            ->leftJoin('bookings', function ($join) use ($dateFrom) {
                $join->on('operators.id', '=', 'bookings.operator_id')
                     ->where('bookings.status', '=', 'completed');
                if ($dateFrom) {
                    $join->where('bookings.completed_at', '>=', $dateFrom);
                }
            })
            ->groupBy('operators.id')
            ->orderByDesc('total_revenue')
            ->limit(20)
            ->get();

        return view('admin.revenue', compact('stats', 'operatorRevenue', 'period'));
    }
}
