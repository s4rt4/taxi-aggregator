<?php

namespace App\Services\Pricing;

use App\Models\DeadLegDiscount;
use App\Models\FlashSale;
use App\Models\FleetType;
use App\Models\MeetGreetCharge;
use App\Models\MeetGreetLocation;
use App\Models\Operator;
use App\Models\Quote;
use App\Models\QuoteSearch;
use App\Models\Vehicle;
use Carbon\Carbon;

class QuoteService
{
    protected PmpCalculator $pmpCalculator;
    protected LpCalculator $lpCalculator;
    protected PapCalculator $papCalculator;
    protected AvailabilityChecker $availabilityChecker;

    public function __construct(
        PmpCalculator $pmp,
        LpCalculator $lp,
        PapCalculator $pap,
        AvailabilityChecker $availabilityChecker
    ) {
        $this->pmpCalculator = $pmp;
        $this->lpCalculator = $lp;
        $this->papCalculator = $pap;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * Generate quotes for a search request.
     *
     * Orchestrates the full quote generation flow:
     * 1. Create a QuoteSearch record from the search parameters
     * 2. Find all approved operators
     * 3. For each operator, check basic availability (not paused, trip range)
     * 4. For each operator's fleet types, run availability + pricing checks
     * 5. Apply modifiers: meet & greet, flash sale, dead leg discount
     * 6. Calculate commission and create Quote records
     * 7. Return QuoteSearch with quotes ordered by total_price
     *
     * @param array $searchData Required keys:
     *   - pickup_address: string
     *   - pickup_postcode: string (used for pricing lookups)
     *   - pickup_lat: float
     *   - pickup_lng: float
     *   - destination_address: string
     *   - destination_postcode: string (used for pricing lookups)
     *   - destination_lat: float
     *   - destination_lng: float
     *   - pickup_datetime: string (Y-m-d H:i:s)
     *   - passenger_count: int
     *   - luggage_count: int (optional, default 0)
     *   - distance_miles: float
     *   - estimated_duration_minutes: int
     *   - user_id: int|null (optional)
     *   - session_id: string|null (optional)
     *   - ip_address: string|null (optional)
     *   - is_return: bool (optional, default false)
     *   - return_datetime: string|null (optional)
     */
    public function generateQuotes(array $searchData): QuoteSearch
    {
        // 1. Create QuoteSearch record
        $quoteSearch = QuoteSearch::create([
            'user_id' => $searchData['user_id'] ?? null,
            'session_id' => $searchData['session_id'] ?? null,
            'pickup_address' => $searchData['pickup_address'],
            'pickup_lat' => $searchData['pickup_lat'],
            'pickup_lng' => $searchData['pickup_lng'],
            'destination_address' => $searchData['destination_address'],
            'destination_lat' => $searchData['destination_lat'],
            'destination_lng' => $searchData['destination_lng'],
            'pickup_datetime' => $searchData['pickup_datetime'],
            'passenger_count' => $searchData['passenger_count'],
            'luggage_count' => $searchData['luggage_count'] ?? 0,
            'distance_miles' => $searchData['distance_miles'],
            'estimated_duration_minutes' => $searchData['estimated_duration_minutes'],
            'is_return' => $searchData['is_return'] ?? false,
            'return_datetime' => $searchData['return_datetime'] ?? null,
            'ip_address' => $searchData['ip_address'] ?? null,
        ]);

        $pickupDatetime = Carbon::parse($searchData['pickup_datetime']);
        $distanceMiles = (float) $searchData['distance_miles'];
        $pickupPostcode = $searchData['pickup_postcode'];
        $destinationPostcode = $searchData['destination_postcode'];
        $passengerCount = (int) $searchData['passenger_count'];

        // 2. Get all approved operators
        $operators = Operator::approved()->get();

        // 3. For each operator, generate quotes
        foreach ($operators as $operator) {
            // Basic operator-level availability (trip range check)
            if (!$this->isOperatorAvailable($operator, $distanceMiles)) {
                continue;
            }

            // 4. Get all fleet types this operator has vehicles for
            $fleetTypeIds = $this->getOperatorFleetTypeIds($operator);

            foreach ($fleetTypeIds as $fleetTypeId) {
                $fleetType = FleetType::find($fleetTypeId);
                if (!$fleetType || !$fleetType->is_active) {
                    continue;
                }

                // Skip fleet types that can't carry enough passengers
                if ($fleetType->max_passengers < $passengerCount) {
                    continue;
                }

                // Check fleet-type-level availability (vehicles, notice, hours, pause)
                if (!$this->availabilityChecker->isAvailable($operator, $fleetTypeId, $pickupDatetime, $distanceMiles)) {
                    continue;
                }

                // 4d. Calculate price using priority: PAP > LP > PMP
                $priceResult = $this->calculatePrice(
                    $operator->id,
                    $fleetTypeId,
                    $pickupPostcode,
                    $destinationPostcode,
                    $distanceMiles
                );

                if ($priceResult === null) {
                    continue; // No pricing configured for this fleet type
                }

                $basePrice = $priceResult['price'];
                $priceSource = $priceResult['source'];

                // 4e. Meet & greet charge
                $meetGreetCharge = $this->getMeetGreetCharge($operator->id, $pickupPostcode, $destinationPostcode);
                $hasMeetAndGreet = $meetGreetCharge > 0;

                // 4f. Flash sale discount
                $flashSaleResult = $this->getFlashSaleDiscount($operator->id, $fleetTypeId, $basePrice, $pickupDatetime);
                $flashSaleDiscount = $flashSaleResult['discount'];
                $flashSaleId = $flashSaleResult['flash_sale_id'];

                // 4g. Dead leg discount
                $deadLegResult = $this->getDeadLegDiscount($operator->id, $pickupPostcode, $destinationPostcode, $basePrice, $pickupDatetime);
                $deadLegDiscount = $deadLegResult['discount'];
                $deadLegDiscountId = $deadLegResult['dead_leg_discount_id'];

                // Calculate total price
                $totalPrice = $basePrice + $meetGreetCharge - $flashSaleDiscount - $deadLegDiscount;
                $totalPrice = max(0, round($totalPrice, 2));

                // Get max passengers and luggage from the fleet type's vehicles for this operator
                $vehicleInfo = $this->getVehicleInfo($operator->id, $fleetTypeId, $fleetType);

                // Create Quote record
                Quote::create([
                    'quote_search_id' => $quoteSearch->id,
                    'operator_id' => $operator->id,
                    'fleet_type_id' => $fleetTypeId,
                    'price_source' => $priceSource,
                    'base_price' => $basePrice,
                    'meet_greet_charge' => $meetGreetCharge,
                    'flash_sale_discount' => $flashSaleDiscount,
                    'dead_leg_discount' => $deadLegDiscount,
                    'surcharges' => 0,
                    'total_price' => $totalPrice,
                    'currency' => 'GBP',
                    'max_passengers' => $vehicleInfo['max_passengers'],
                    'max_luggage' => $vehicleInfo['max_luggage'],
                    'fleet_type_name' => $fleetType->name,
                    'operator_name' => $operator->operator_name,
                    'operator_rating' => $operator->rating_avg ?? 0,
                    'estimated_duration_minutes' => $searchData['estimated_duration_minutes'],
                    'meet_and_greet' => $hasMeetAndGreet,
                    'flash_sale_id' => $flashSaleId,
                    'dead_leg_discount_id' => $deadLegDiscountId,
                    'is_available' => true,
                    'expires_at' => Carbon::now()->addMinutes(30),
                ]);
            }
        }

        // 5. Return QuoteSearch with quotes ordered by total_price
        return $quoteSearch->load(['quotes' => function ($query) {
            $query->orderBy('total_price', 'asc');
        }]);
    }

    /**
     * Calculate the best price for a single operator + fleet type.
     * Tries PAP > LP > PMP priority.
     *
     * @return array{price: float, source: string}|null
     */
    public function calculatePrice(
        int $operatorId,
        int $fleetTypeId,
        string $pickupPostcode,
        string $destinationPostcode,
        float $distanceMiles
    ): ?array {
        // Try PAP first (highest priority)
        $price = $this->papCalculator->calculate($operatorId, $fleetTypeId, $pickupPostcode, $destinationPostcode);
        if ($price !== null) {
            return ['price' => $price, 'source' => 'pap'];
        }

        // Try LP (second priority)
        $price = $this->lpCalculator->calculate($operatorId, $fleetTypeId, $pickupPostcode, $destinationPostcode);
        if ($price !== null) {
            return ['price' => $price, 'source' => 'lp'];
        }

        // Try PMP (fallback)
        $price = $this->pmpCalculator->calculate($operatorId, $fleetTypeId, $distanceMiles);
        if ($price !== null) {
            return ['price' => $price, 'source' => 'pmp'];
        }

        return null;
    }

    /**
     * Check if an operator is available for a booking at the operator level.
     * This checks conditions that apply to the operator as a whole,
     * not per fleet type (trip range, status, etc.).
     */
    protected function isOperatorAvailable(Operator $operator, float $distanceMiles): bool
    {
        // Operator must be approved (already filtered by scope, but double-check)
        if ($operator->status !== 'approved') {
            return false;
        }

        // Check trip range at operator level
        $tripRange = $operator->tripRange;
        if ($tripRange && $distanceMiles > $tripRange->dropoff_range_miles) {
            return false;
        }

        return true;
    }

    /**
     * Get all distinct fleet type IDs this operator has vehicles for.
     *
     * @return array<int>
     */
    protected function getOperatorFleetTypeIds(Operator $operator): array
    {
        return Vehicle::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->distinct()
            ->pluck('fleet_type_id')
            ->toArray();
    }

    /**
     * Get applicable meet & greet charge.
     *
     * Checks if the pickup or destination postcode is near a meet & greet location
     * (by matching the location code to the postcode prefix/area).
     * Returns the charge amount or 0 if not applicable.
     */
    protected function getMeetGreetCharge(int $operatorId, string $pickupPostcode, string $destinationPostcode): float
    {
        // Look up meet & greet locations by code matching the postcode area
        // Airports/stations often use IATA codes, but we match by lat/lng proximity
        // For simplicity, we match by the location code against the postcode
        $locationIds = MeetGreetLocation::where('is_active', true)
            ->pluck('id', 'code')
            ->toArray();

        if (empty($locationIds)) {
            return 0.0;
        }

        // Find if operator has any active meet & greet charges for these locations
        $charge = MeetGreetCharge::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->whereIn('meet_greet_location_id', array_values($locationIds))
            ->orderBy('charge', 'desc')
            ->first();

        return $charge ? (float) $charge->charge : 0.0;
    }

    /**
     * Get applicable flash sale discount.
     *
     * Finds active flash sales for this operator that cover the pickup date
     * and apply to the given fleet type.
     *
     * @return array{discount: float, flash_sale_id: int|null}
     */
    protected function getFlashSaleDiscount(int $operatorId, int $fleetTypeId, float $basePrice, Carbon $pickupDate): array
    {
        $noDiscount = ['discount' => 0.0, 'flash_sale_id' => null];

        $flashSale = FlashSale::where('operator_id', $operatorId)
            ->active()
            ->where(function ($query) use ($fleetTypeId) {
                // Either applies to all fleet types, or specifically includes this one
                $query->where('all_fleet_types', true)
                    ->orWhereHas('fleetTypes', function ($q) use ($fleetTypeId) {
                        $q->where('fleet_type_id', $fleetTypeId);
                    });
            })
            ->first();

        if (!$flashSale) {
            return $noDiscount;
        }

        $discount = 0.0;

        if ($flashSale->discount_type === 'percentage') {
            $discount = round($basePrice * ((float) $flashSale->discount_value / 100), 2);
        } elseif ($flashSale->discount_type === 'fixed') {
            $discount = min((float) $flashSale->discount_value, $basePrice);
        }

        return [
            'discount' => $discount,
            'flash_sale_id' => $flashSale->id,
        ];
    }

    /**
     * Get applicable dead leg discount.
     *
     * Searches for active dead leg discounts where:
     * - The operator matches
     * - The from_area matches the pickup postcode area
     * - The to_area matches the destination postcode area
     * - The pickup datetime falls within the available window
     *
     * @return array{discount: float, dead_leg_discount_id: int|null}
     */
    protected function getDeadLegDiscount(
        int $operatorId,
        string $pickupPostcode,
        string $destinationPostcode,
        float $basePrice,
        Carbon $pickupDatetime
    ): array {
        $noDiscount = ['discount' => 0.0, 'dead_leg_discount_id' => null];

        $pickupArea = PapCalculator::extractPostcodeArea($pickupPostcode);
        $destinationArea = PapCalculator::extractPostcodeArea($destinationPostcode);

        $deadLeg = DeadLegDiscount::where('operator_id', $operatorId)
            ->active()
            ->whereRaw('UPPER(from_area) = ?', [strtoupper($pickupArea)])
            ->whereRaw('UPPER(to_area) = ?', [strtoupper($destinationArea)])
            ->first();

        if (!$deadLeg) {
            return $noDiscount;
        }

        $discount = 0.0;

        if ($deadLeg->discount_type === 'percentage') {
            $discount = round($basePrice * ((float) $deadLeg->discount_value / 100), 2);
        } elseif ($deadLeg->discount_type === 'fixed') {
            $discount = min((float) $deadLeg->discount_value, $basePrice);
        }

        return [
            'discount' => $discount,
            'dead_leg_discount_id' => $deadLeg->id,
        ];
    }

    /**
     * Get vehicle capacity info for the given operator + fleet type.
     * Falls back to the fleet type's own max_passengers if no vehicles found.
     *
     * @return array{max_passengers: int, max_luggage: int}
     */
    protected function getVehicleInfo(int $operatorId, int $fleetTypeId, FleetType $fleetType): array
    {
        $vehicle = Vehicle::where('operator_id', $operatorId)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('is_active', true)
            ->first();

        return [
            'max_passengers' => $vehicle ? $vehicle->max_passengers : $fleetType->max_passengers,
            'max_luggage' => $vehicle ? ($vehicle->max_luggage ?? 0) : 0,
        ];
    }
}
