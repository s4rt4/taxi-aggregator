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
        ]);

        // For now, use mock distance since Google Maps API isn't integrated yet
        // In production, this would call Google Distance Matrix API
        $distanceMiles = 15.0; // placeholder

        $pickupDatetime = $request->pickup_date . ' ' . $request->pickup_time;

        $quoteSearch = $quoteService->generateQuotes([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'pickup_address' => $request->pickup_address,
            'pickup_lat' => 0, // placeholder for Google Maps
            'pickup_lng' => 0,
            'pickup_postcode' => '', // placeholder for Google Maps
            'destination_address' => $request->destination_address,
            'destination_lat' => 0,
            'destination_lng' => 0,
            'destination_postcode' => '', // placeholder for Google Maps
            'pickup_datetime' => $pickupDatetime,
            'passenger_count' => $request->passengers,
            'luggage_count' => $request->input('luggage', 0),
            'distance_miles' => $distanceMiles,
            'estimated_duration_minutes' => 30, // placeholder
            'is_return' => false,
            'ip_address' => $request->ip(),
        ]);

        $quotes = $quoteSearch->quotes()->with('operator')->orderBy('total_price')->get();

        return view('search.results', compact('quoteSearch', 'quotes'));
    }
}
