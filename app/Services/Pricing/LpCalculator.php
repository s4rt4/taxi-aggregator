<?php

namespace App\Services\Pricing;

use App\Models\LocationPrice;

class LpCalculator
{
    /**
     * Find matching location price for operator, fleet type, and postcodes.
     *
     * Searches active LocationPrice records where:
     * - start_postcode prefix matches pickup AND finish_postcode prefix matches destination
     * - OR (also_reverse=true AND start matches destination AND finish matches pickup)
     *
     * @return float|null Price or null if no LP match
     */
    public function calculate(int $operatorId, int $fleetTypeId, string $pickupPostcode, string $destinationPostcode): ?float
    {
        $pickupPrefix = self::extractPostcodePrefix($pickupPostcode);
        $destinationPrefix = self::extractPostcodePrefix($destinationPostcode);

        $match = LocationPrice::where('operator_id', $operatorId)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('is_active', true)
            ->where(function ($query) use ($pickupPrefix, $destinationPrefix) {
                // Forward direction: start matches pickup, finish matches destination
                $query->where(function ($q) use ($pickupPrefix, $destinationPrefix) {
                    $q->whereRaw('UPPER(start_postcode) = ?', [$pickupPrefix])
                      ->whereRaw('UPPER(finish_postcode) = ?', [$destinationPrefix]);
                })
                // Reverse direction: also_reverse=true AND start matches destination, finish matches pickup
                ->orWhere(function ($q) use ($pickupPrefix, $destinationPrefix) {
                    $q->where('also_reverse', true)
                      ->whereRaw('UPPER(start_postcode) = ?', [$destinationPrefix])
                      ->whereRaw('UPPER(finish_postcode) = ?', [$pickupPrefix]);
                });
            })
            ->first();

        return $match ? (float) $match->price : null;
    }

    /**
     * Extract postcode prefix (outward code) for matching.
     *
     * UK postcodes have an outward code (before the space) and an inward code (after).
     * Examples:
     *   "SO14 2AA" -> "SO14"
     *   "SW1A 1AA" -> "SW1A"
     *   "B1 1AA"   -> "B1"
     *   "EC1A 1BB" -> "EC1A"
     *   "so14 2aa" -> "SO14"
     *   "SO142AA"  -> "SO14" (no space - take all but last 3 chars)
     */
    public static function extractPostcodePrefix(string $postcode): string
    {
        $postcode = strtoupper(trim($postcode));

        // If it contains a space, the outward code is everything before the space
        if (str_contains($postcode, ' ')) {
            return explode(' ', $postcode)[0];
        }

        // No space: the inward code is always the last 3 characters (digit + 2 letters)
        // so the outward code is everything before that
        if (strlen($postcode) > 3) {
            return substr($postcode, 0, -3);
        }

        // Short input - return as-is (likely already just the prefix)
        return $postcode;
    }

    /**
     * Find all LP matches for an operator given pickup/destination postcodes.
     * Returns prices for all fleet types that have matches.
     *
     * @return array<int, float> [fleet_type_id => price]
     */
    public function calculateAll(int $operatorId, string $pickupPostcode, string $destinationPostcode): array
    {
        $pickupPrefix = self::extractPostcodePrefix($pickupPostcode);
        $destinationPrefix = self::extractPostcodePrefix($destinationPostcode);

        $matches = LocationPrice::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->where(function ($query) use ($pickupPrefix, $destinationPrefix) {
                // Forward direction
                $query->where(function ($q) use ($pickupPrefix, $destinationPrefix) {
                    $q->whereRaw('UPPER(start_postcode) = ?', [$pickupPrefix])
                      ->whereRaw('UPPER(finish_postcode) = ?', [$destinationPrefix]);
                })
                // Reverse direction
                ->orWhere(function ($q) use ($pickupPrefix, $destinationPrefix) {
                    $q->where('also_reverse', true)
                      ->whereRaw('UPPER(start_postcode) = ?', [$destinationPrefix])
                      ->whereRaw('UPPER(finish_postcode) = ?', [$pickupPrefix]);
                });
            })
            ->get();

        $results = [];
        foreach ($matches as $match) {
            $fleetTypeId = $match->fleet_type_id;
            // If multiple matches exist for the same fleet type, take the first one found
            if (!isset($results[$fleetTypeId])) {
                $results[$fleetTypeId] = (float) $match->price;
            }
        }

        return $results;
    }
}
