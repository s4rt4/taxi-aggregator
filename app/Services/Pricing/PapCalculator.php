<?php

namespace App\Services\Pricing;

use App\Models\PostcodeAreaPrice;

class PapCalculator
{
    /**
     * Find PAP price for operator, fleet type, and postcode areas.
     *
     * Extracts the postcode area (leading letters) from full postcodes,
     * then looks up the PostcodeAreaPrice for the given operator/fleet type pair.
     *
     * This is the HIGHEST priority pricing method (PAP > LP > PMP).
     *
     * @return float|null Price or null if no PAP configured
     */
    public function calculate(int $operatorId, int $fleetTypeId, string $pickupPostcode, string $destinationPostcode): ?float
    {
        $fromArea = self::extractPostcodeArea($pickupPostcode);
        $toArea = self::extractPostcodeArea($destinationPostcode);

        $match = PostcodeAreaPrice::where('operator_id', $operatorId)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('is_active', true)
            ->whereRaw('UPPER(from_postcode_area) = ?', [$fromArea])
            ->whereRaw('UPPER(to_postcode_area) = ?', [$toArea])
            ->first();

        return $match ? (float) $match->price : null;
    }

    /**
     * Extract postcode AREA from a full UK postcode.
     *
     * The area is the leading alphabetic characters (1-2 letters) of the outward code.
     * Examples:
     *   "SO14 2AA" -> "SO"
     *   "SW1A 1AA" -> "SW"
     *   "EC1A 1BB" -> "EC"
     *   "B1 1AA"   -> "B"
     *   "AB10 1CD" -> "AB"
     *   "W1A 1AA"  -> "W"
     *   "so14 2aa" -> "SO"
     */
    public static function extractPostcodeArea(string $postcode): string
    {
        $postcode = strtoupper(trim($postcode));

        // Extract leading alpha characters only
        if (preg_match('/^([A-Z]{1,2})/', $postcode, $matches)) {
            return $matches[1];
        }

        return $postcode;
    }

    /**
     * Find all PAP matches for an operator given pickup/destination postcodes.
     * Returns prices for all fleet types that have matches.
     *
     * @return array<int, float> [fleet_type_id => price]
     */
    public function calculateAll(int $operatorId, string $pickupPostcode, string $destinationPostcode): array
    {
        $fromArea = self::extractPostcodeArea($pickupPostcode);
        $toArea = self::extractPostcodeArea($destinationPostcode);

        $matches = PostcodeAreaPrice::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->whereRaw('UPPER(from_postcode_area) = ?', [$fromArea])
            ->whereRaw('UPPER(to_postcode_area) = ?', [$toArea])
            ->get();

        $results = [];
        foreach ($matches as $match) {
            $fleetTypeId = $match->fleet_type_id;
            if (!isset($results[$fleetTypeId])) {
                $results[$fleetTypeId] = (float) $match->price;
            }
        }

        return $results;
    }
}
