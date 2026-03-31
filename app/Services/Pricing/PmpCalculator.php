<?php

namespace App\Services\Pricing;

use App\Models\PerMilePrice;
use App\Models\PerMilePriceRange;
use App\Models\PerMileUplift;

class PmpCalculator
{
    /**
     * Calculate PMP price for given operator, fleet type, and distance.
     *
     * The calculation follows this sequence:
     * 1. Look up the PerMilePrice record for this operator + fleet type
     * 2. Determine the applicable rate per mile (range-specific or base)
     * 3. Multiply distance by rate
     * 4. Apply any fleet-type uplift percentage for this distance band
     * 5. Enforce minimum fare
     * 6. Round to 2 decimal places
     *
     * @return float|null Price in GBP, or null if no active PMP configured
     */
    public function calculate(int $operatorId, int $fleetTypeId, float $distanceMiles): ?float
    {
        $pmp = PerMilePrice::where('operator_id', $operatorId)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('is_active', true)
            ->first();

        if (!$pmp) {
            return null;
        }

        $ratePerMile = $this->getRateForDistance($pmp, $distanceMiles);

        $price = $distanceMiles * $ratePerMile;

        $upliftPercentage = $this->getUpliftPercentage($operatorId, $fleetTypeId, $distanceMiles);
        if ($upliftPercentage > 0) {
            $price *= (1 + $upliftPercentage / 100);
        }

        $minimumFare = (float) $pmp->minimum_fare;
        $price = max($price, $minimumFare);

        return round($price, 2);
    }

    /**
     * Get the applicable rate per mile for a given distance.
     *
     * Checks mileage range brackets first (mile_from <= distance AND
     * mile_to >= distance OR mile_to IS NULL for open-ended ranges).
     * Falls back to the base rate_per_mile on the PerMilePrice record.
     */
    protected function getRateForDistance(PerMilePrice $pmp, float $distanceMiles): float
    {
        $range = PerMilePriceRange::where('per_mile_price_id', $pmp->id)
            ->where('mile_from', '<=', $distanceMiles)
            ->where(function ($query) use ($distanceMiles) {
                $query->where('mile_to', '>=', $distanceMiles)
                    ->orWhereNull('mile_to');
            })
            ->first();

        if ($range) {
            return (float) $range->rate_per_mile;
        }

        return (float) $pmp->rate_per_mile;
    }

    /**
     * Get the uplift percentage for a fleet type at a given distance.
     *
     * Searches the per_mile_uplifts table for a matching record where
     * the distance falls within the mile_from/mile_to band.
     *
     * @return float Uplift percentage (e.g. 40.00 means +40%), or 0 if none
     */
    protected function getUpliftPercentage(int $operatorId, int $fleetTypeId, float $distanceMiles): float
    {
        $uplift = PerMileUplift::where('operator_id', $operatorId)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('mile_from', '<=', $distanceMiles)
            ->where(function ($query) use ($distanceMiles) {
                $query->where('mile_to', '>=', $distanceMiles)
                    ->orWhereNull('mile_to');
            })
            ->first();

        if ($uplift) {
            return (float) $uplift->uplift_percentage;
        }

        return 0.0;
    }

    /**
     * Calculate prices for ALL active fleet types for a given operator and distance.
     *
     * Used for quote comparison -- returns a price for every fleet type
     * the operator has configured PMP pricing for.
     *
     * @return array<int, float> Keyed by fleet_type_id => price in GBP
     */
    public function calculateAll(int $operatorId, float $distanceMiles): array
    {
        $pmps = PerMilePrice::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->get();

        $prices = [];

        foreach ($pmps as $pmp) {
            $price = $this->calculate($operatorId, $pmp->fleet_type_id, $distanceMiles);
            if ($price !== null) {
                $prices[$pmp->fleet_type_id] = $price;
            }
        }

        return $prices;
    }
}
