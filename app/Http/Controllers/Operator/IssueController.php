<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $operator = auth()->user()->operator;

        // Weekly trip issue stats (used by the Trip Issues tab)
        $weeklyStats = $operator?->weeklyStats()->latest('week_start')->paginate(10)
            ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        // Latest reviews (used by the Latest Ratings tab)
        $reviews = $operator?->reviews()->with('booking')->latest()->limit(10)->get()
            ?? collect();

        // Rating summary averages (used by the bar chart on Latest Ratings tab)
        $ratingSummary = [
            'timing' => $operator?->reviews()->avg('timing_rating') ?? 0,
            'fare' => $operator?->reviews()->avg('fare_rating') ?? 0,
            'driver' => $operator?->reviews()->avg('driver_rating') ?? 0,
            'vehicle' => $operator?->reviews()->avg('vehicle_rating') ?? 0,
            'route' => $operator?->reviews()->avg('route_rating') ?? 0,
        ];

        $totalRatedTrips = $operator?->reviews()->count() ?? 0;

        // Aliases used in the view templates
        $tripIssues = $weeklyStats;
        $latestRatings = $reviews;

        return view('operator.issues.index', compact(
            'weeklyStats',
            'reviews',
            'ratingSummary',
            'totalRatedTrips',
            'tripIssues',
            'latestRatings'
        ));
    }
}
