<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FleetType;
use App\Models\LocationPrice;
use App\Models\MeetGreetCharge;
use App\Models\MeetGreetLocation;
use App\Models\PerMilePrice;
use App\Models\PerMilePriceRange;
use App\Models\PerMileUplift;
use App\Models\PostcodeArea;
use App\Models\PostcodeAreaPrice;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    /**
     * Get the authenticated user's operator.
     */
    protected function getOperator()
    {
        return auth()->user()->operator;
    }

    // =========================================================================
    // Per Mile Prices
    // =========================================================================

    /**
     * Show the per-mile pricing page.
     */
    public function perMile()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $perMilePrices = $operator?->perMilePrices()->with('ranges')->get()->keyBy('fleet_type_id') ?? collect();
        $uplifts = $operator?->perMileUplifts()->get() ?? collect();

        return view('operator.pricing.per-mile', compact('fleetTypes', 'perMilePrices', 'uplifts'));
    }

    /**
     * Save per-mile pricing data (rates, ranges, and uplifts).
     */
    public function savePerMile(Request $request)
    {
        $request->validate([
            'rates' => ['nullable', 'array'],
            'rates.*.rate_per_mile' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'rates.*.minimum_fare' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'ranges' => ['nullable', 'array'],
            'ranges.*' => ['nullable', 'array'],
            'ranges.*.*.mile_from' => ['required_with:ranges.*.*.rate_per_mile', 'nullable', 'integer', 'min:0'],
            'ranges.*.*.mile_to' => ['required_with:ranges.*.*.rate_per_mile', 'nullable', 'integer', 'min:0'],
            'ranges.*.*.rate_per_mile' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'uplifts' => ['nullable', 'array'],
            'uplifts.*' => ['nullable', 'array'],
            'uplifts.*.mile_from' => ['nullable', 'integer', 'min:0'],
            'uplifts.*.mile_to' => ['nullable', 'integer', 'min:0'],
            'uplifts.*.uplift_percentage' => ['nullable', 'numeric', 'min:0', 'max:500'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Upsert per-mile base rates for each fleet type
        foreach ($request->input('rates', []) as $fleetTypeId => $rateData) {
            if (empty($rateData['rate_per_mile']) && empty($rateData['minimum_fare'])) {
                // Remove if both empty
                $operator->perMilePrices()->where('fleet_type_id', $fleetTypeId)->delete();
                continue;
            }

            $perMilePrice = PerMilePrice::updateOrCreate(
                [
                    'operator_id' => $operator->id,
                    'fleet_type_id' => $fleetTypeId,
                ],
                [
                    'rate_per_mile' => $rateData['rate_per_mile'] ?? 0,
                    'minimum_fare' => $rateData['minimum_fare'] ?? 0,
                    'is_active' => true,
                ]
            );

            // Upsert mileage bracket ranges for this fleet type
            $perMilePrice->ranges()->delete();

            if (isset($request->input('ranges', [])[$fleetTypeId])) {
                foreach ($request->input('ranges')[$fleetTypeId] as $rangeData) {
                    if (!empty($rangeData['rate_per_mile']) && isset($rangeData['mile_from']) && isset($rangeData['mile_to'])) {
                        $perMilePrice->ranges()->create([
                            'mile_from' => $rangeData['mile_from'],
                            'mile_to' => $rangeData['mile_to'],
                            'rate_per_mile' => $rangeData['rate_per_mile'],
                        ]);
                    }
                }
            }
        }

        // Upsert uplift records
        $operator->perMileUplifts()->delete();

        foreach ($request->input('uplifts', []) as $fleetTypeId => $upliftData) {
            if (!empty($upliftData['uplift_percentage']) && isset($upliftData['mile_from']) && isset($upliftData['mile_to'])) {
                PerMileUplift::create([
                    'operator_id' => $operator->id,
                    'fleet_type_id' => $fleetTypeId,
                    'mile_from' => $upliftData['mile_from'],
                    'mile_to' => $upliftData['mile_to'],
                    'uplift_percentage' => $upliftData['uplift_percentage'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Per mile pricing saved successfully.');
    }

    // =========================================================================
    // Location Prices
    // =========================================================================

    /**
     * Show the location-based pricing page.
     */
    public function location()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $locationPrices = $operator
            ? $operator->locationPrices()->with('fleetType')->latest()->paginate(20)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('operator.pricing.location', compact('fleetTypes', 'locationPrices'));
    }

    /**
     * Store a new location price.
     */
    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'fleet_type_id' => ['required', 'exists:fleet_types,id'],
            'start_postcode' => ['required', 'string', 'max:10'],
            'start_radius_miles' => ['required', 'integer', 'min:1', 'max:100'],
            'finish_postcode' => ['required', 'string', 'max:10'],
            'finish_radius_miles' => ['required', 'integer', 'min:1', 'max:100'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'also_reverse' => ['nullable', 'boolean'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Create the forward direction price
        LocationPrice::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $validated['fleet_type_id'],
            'start_postcode' => strtoupper(trim($validated['start_postcode'])),
            'start_radius_miles' => $validated['start_radius_miles'],
            'finish_postcode' => strtoupper(trim($validated['finish_postcode'])),
            'finish_radius_miles' => $validated['finish_radius_miles'],
            'price' => $validated['price'],
            'also_reverse' => $validated['also_reverse'] ?? false,
            'is_active' => true,
        ]);

        // If also_reverse, create the reverse direction too
        if (!empty($validated['also_reverse'])) {
            LocationPrice::create([
                'operator_id' => $operator->id,
                'fleet_type_id' => $validated['fleet_type_id'],
                'start_postcode' => strtoupper(trim($validated['finish_postcode'])),
                'start_radius_miles' => $validated['finish_radius_miles'],
                'finish_postcode' => strtoupper(trim($validated['start_postcode'])),
                'finish_radius_miles' => $validated['start_radius_miles'],
                'price' => $validated['price'],
                'also_reverse' => true,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Location price added successfully.');
    }

    /**
     * Delete a location price.
     */
    public function destroyLocation($id)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            abort(403, 'Unauthorized action.');
        }

        $locationPrice = LocationPrice::where('id', $id)
            ->where('operator_id', $operator->id)
            ->firstOrFail();

        $locationPrice->delete();

        return redirect()->back()->with('success', 'Location price removed successfully.');
    }

    // =========================================================================
    // Postcode Area Prices
    // =========================================================================

    /**
     * Show the postcode area pricing grid.
     */
    public function postcodeArea()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $postcodeAreas = PostcodeArea::active()->orderBy('code')->get();
        $prices = $operator?->postcodeAreaPrices()->get() ?? collect();

        return view('operator.pricing.postcode-area', compact('fleetTypes', 'postcodeAreas', 'prices'));
    }

    /**
     * Bulk save postcode area prices from the grid form.
     */
    public function savePostcodeArea(Request $request)
    {
        $request->validate([
            'fleet_type_id' => ['required', 'exists:fleet_types,id'],
            'prices' => ['nullable', 'array'],
            'prices.*.*' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        $fleetTypeId = $request->input('fleet_type_id');

        // Remove existing prices for this fleet type, then bulk insert
        $operator->postcodeAreaPrices()
            ->where('fleet_type_id', $fleetTypeId)
            ->delete();

        foreach ($request->input('prices', []) as $fromArea => $toAreas) {
            foreach ($toAreas as $toArea => $price) {
                if ($price !== null && $price !== '') {
                    PostcodeAreaPrice::create([
                        'operator_id' => $operator->id,
                        'fleet_type_id' => $fleetTypeId,
                        'from_postcode_area' => $fromArea,
                        'to_postcode_area' => $toArea,
                        'price' => $price,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Postcode area prices saved successfully.');
    }

    // =========================================================================
    // Meet & Greet Charges
    // =========================================================================

    /**
     * Show the meet & greet charges page.
     */
    public function meetGreet()
    {
        $operator = $this->getOperator();
        $locations = MeetGreetLocation::where('is_active', true)->orderBy('name')->get();
        $charges = $operator?->meetGreetCharges()->get()->keyBy('meet_greet_location_id') ?? collect();

        return view('operator.pricing.meet-greet', compact('locations', 'charges'));
    }

    /**
     * Save meet & greet charge records.
     */
    public function saveMeetGreet(Request $request)
    {
        $request->validate([
            'charges' => ['nullable', 'array'],
            'charges.*' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        foreach ($request->input('charges', []) as $locationId => $chargeAmount) {
            if ($chargeAmount !== null && $chargeAmount !== '') {
                MeetGreetCharge::updateOrCreate(
                    [
                        'operator_id' => $operator->id,
                        'meet_greet_location_id' => $locationId,
                    ],
                    [
                        'charge' => $chargeAmount,
                        'is_active' => true,
                    ]
                );
            } else {
                // Remove charge if cleared
                $operator->meetGreetCharges()
                    ->where('meet_greet_location_id', $locationId)
                    ->delete();
            }
        }

        return redirect()->back()->with('success', 'Meet & greet charges saved successfully.');
    }

    // =========================================================================
    // Flash Sales
    // =========================================================================

    /**
     * Show the flash sales page.
     */
    public function flashSales()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $liveSales = $operator?->flashSales()->where('status', 'active')->latest()->get() ?? collect();
        $expiredSales = $operator?->flashSales()
            ->whereIn('status', ['expired', 'disabled'])
            ->latest()
            ->limit(20)
            ->get() ?? collect();

        return view('operator.pricing.flash-sales', compact('fleetTypes', 'liveSales', 'expiredSales'));
    }

    /**
     * Create a new flash sale.
     */
    public function storeFlashSale(Request $request)
    {
        $validated = $request->validate([
            'starts_at' => ['required', 'date', 'after_or_equal:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'all_fleet_types' => ['nullable', 'boolean'],
            'fleet_type_ids' => ['nullable', 'array'],
            'fleet_type_ids.*' => ['exists:fleet_types,id'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Additional validation: percentage must be <= 100
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%.']);
        }

        $allFleetTypes = $validated['all_fleet_types'] ?? (empty($validated['fleet_type_ids']));

        $flashSale = FlashSale::create([
            'operator_id' => $operator->id,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'all_fleet_types' => $allFleetTypes,
            'all_routes' => true,
            'status' => 'active',
        ]);

        // Attach specific fleet types if not "all"
        if (!$allFleetTypes && !empty($validated['fleet_type_ids'])) {
            $flashSale->fleetTypes()->attach($validated['fleet_type_ids']);
        }

        return redirect()->back()->with('success', 'Flash sale created successfully.');
    }

    /**
     * Disable an active flash sale.
     */
    public function disableFlashSale($id)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            abort(403, 'Unauthorized action.');
        }

        $flashSale = FlashSale::where('id', $id)
            ->where('operator_id', $operator->id)
            ->where('status', 'active')
            ->firstOrFail();

        $flashSale->update(['status' => 'disabled']);

        return redirect()->back()->with('success', 'Flash sale disabled successfully.');
    }

    // =========================================================================
    // Dead Leg Discounts
    // =========================================================================

    /**
     * Show the dead leg discounts page.
     */
    public function deadLeg()
    {
        $operator = $this->getOperator();
        $discounts = $operator
            ? $operator->deadLegDiscounts()->latest()->paginate(20)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('operator.pricing.dead-leg', compact('discounts'));
    }

    // =========================================================================
    // More Pricing Options
    // =========================================================================

    /**
     * Show the additional pricing options page.
     */
    public function more()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $freePickupPostcodes = $operator?->freePickupPostcodes()->pluck('postcode_area') ?? collect();

        return view('operator.pricing.more', compact('fleetTypes', 'freePickupPostcodes'));
    }

    /**
     * Sync free pickup postcodes for the operator.
     */
    public function saveFreePickupPostcodes(Request $request)
    {
        $request->validate([
            'postcodes' => ['nullable', 'array'],
            'postcodes.*' => ['string', 'max:10'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Clear existing and re-insert
        $operator->freePickupPostcodes()->delete();

        $postcodes = array_filter(array_map('trim', $request->input('postcodes', [])));
        $postcodes = array_unique(array_map('strtoupper', $postcodes));

        foreach ($postcodes as $postcode) {
            if (!empty($postcode)) {
                $operator->freePickupPostcodes()->create([
                    'postcode_area' => $postcode,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Free pickup postcodes saved successfully.');
    }
}
