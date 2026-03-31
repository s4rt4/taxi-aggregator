<?php

namespace App\Http\Controllers;

use App\Services\Pricing\QuoteService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request, QuoteService $quoteService)
    {
        $request->validate([
            'pickup_address' => 'required|string',
            'destination_address' => 'required|string',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required',
            'passengers' => 'required|integer|min:1|max:16',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'destination_lat' => 'nullable|numeric',
            'destination_lng' => 'nullable|numeric',
            'distance_miles' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
        ]);

        // Use real distance from Google Maps Distance Matrix if available, otherwise fallback
        $distanceMiles = max(1, (float) $request->input('distance_miles', 15));
        $estimatedMinutes = max(5, (int) $request->input('estimated_duration_minutes', 30));

        $pickupDatetime = $request->pickup_date . ' ' . $request->pickup_time;

        $quoteSearch = $quoteService->generateQuotes([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'pickup_address' => $request->pickup_address,
            'pickup_lat' => $request->input('pickup_lat', 0),
            'pickup_lng' => $request->input('pickup_lng', 0),
            'pickup_postcode' => '',
            'destination_address' => $request->destination_address,
            'destination_lat' => $request->input('destination_lat', 0),
            'destination_lng' => $request->input('destination_lng', 0),
            'destination_postcode' => '',
            'pickup_datetime' => $pickupDatetime,
            'passenger_count' => $request->passengers,
            'luggage_count' => $request->input('luggage', 0),
            'distance_miles' => $distanceMiles,
            'estimated_duration_minutes' => $estimatedMinutes,
            'is_return' => false,
            'ip_address' => $request->ip(),
        ]);

        $quotes = $quoteSearch->quotes()->with('operator')->orderBy('total_price')->get();

        return view('search.results', compact('quoteSearch', 'quotes'));
    }
}
