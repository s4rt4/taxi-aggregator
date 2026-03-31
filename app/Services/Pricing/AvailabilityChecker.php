<?php

namespace App\Services\Pricing;

use App\Models\AvailabilityPause;
use App\Models\NoticePeriod;
use App\Models\OperatingHour;
use App\Models\Operator;
use App\Models\TripRange;
use App\Models\VehicleAvailability;
use Carbon\Carbon;

class AvailabilityChecker
{
    /**
     * Check if operator has fleet type available at given date/time and distance.
     *
     * Runs all availability checks in sequence:
     * 1. Vehicle count > 0 for the day of week
     * 2. Notice period is met (enough hours before pickup)
     * 3. Pickup time falls within operating hours
     * 4. Trip distance is within operator's trip range
     * 5. Operator is not paused for this fleet type at this time
     */
    public function isAvailable(Operator $operator, int $fleetTypeId, Carbon $pickupDatetime, float $distanceMiles): bool
    {
        return $this->hasVehicles($operator, $fleetTypeId, $pickupDatetime)
            && $this->meetsNoticePeriod($operator, $fleetTypeId, $pickupDatetime)
            && $this->withinOperatingHours($operator, $fleetTypeId, $pickupDatetime)
            && $this->withinTripRange($operator, $distanceMiles)
            && !$this->isPaused($operator, $fleetTypeId, $pickupDatetime);
    }

    /**
     * Check if operator has vehicles available for this fleet type on the given day.
     *
     * Looks up the VehicleAvailability record for the operator + fleet type + day of week.
     * If same_every_day is true on the Monday record, that value applies to all days.
     * Returns true if max_vehicles > 0.
     */
    protected function hasVehicles(Operator $operator, int $fleetTypeId, Carbon $date): bool
    {
        $dayOfWeek = strtolower(substr($date->format('D'), 0, 3));

        // First check the specific day
        $availability = VehicleAvailability::where('operator_id', $operator->id)
            ->where('fleet_type_id', $fleetTypeId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // If no record for this day, check if there's a Monday record with same_every_day
        if (!$availability) {
            $availability = VehicleAvailability::where('operator_id', $operator->id)
                ->where('fleet_type_id', $fleetTypeId)
                ->where('same_every_day', true)
                ->first();
        }

        // No availability record at all means operator hasn't configured this fleet type
        if (!$availability) {
            return false;
        }

        return $availability->max_vehicles > 0;
    }

    /**
     * Check if the pickup datetime meets the operator's notice period requirement.
     *
     * The notice period is the minimum number of hours before pickup that a booking
     * must be made. If the current time + notice hours > pickup time, the operator
     * cannot accept the booking.
     */
    protected function meetsNoticePeriod(Operator $operator, int $fleetTypeId, Carbon $pickupDatetime): bool
    {
        $noticePeriod = NoticePeriod::where('operator_id', $operator->id)
            ->where('fleet_type_id', $fleetTypeId)
            ->first();

        // No notice period configured = no restriction
        if (!$noticePeriod) {
            return true;
        }

        $earliestPickup = Carbon::now()->addHours($noticePeriod->hours_notice);

        return $pickupDatetime->greaterThanOrEqualTo($earliestPickup);
    }

    /**
     * Check if the pickup time falls within the operator's operating hours
     * for this fleet type.
     *
     * Checks:
     * - If is_24_hours is true, always available (time-wise)
     * - If excluded_days includes the pickup day, not available
     * - If start_time/end_time are set, pickup time must be within that window
     */
    protected function withinOperatingHours(Operator $operator, int $fleetTypeId, Carbon $pickupDatetime): bool
    {
        $operatingHour = OperatingHour::where('operator_id', $operator->id)
            ->where('fleet_type_id', $fleetTypeId)
            ->first();

        // No operating hours configured = assume 24/7
        if (!$operatingHour) {
            return true;
        }

        // Check if the day is excluded
        $excludedDays = $operatingHour->excluded_days ?? [];
        $dayOfWeek = strtolower(substr($pickupDatetime->format('D'), 0, 3));

        if (in_array($dayOfWeek, $excludedDays)) {
            return false;
        }

        // If 24 hours, no time restriction
        if ($operatingHour->is_24_hours) {
            return true;
        }

        // Check time window
        $pickupTime = $pickupDatetime->format('H:i:s');
        $startTime = $operatingHour->start_time;
        $endTime = $operatingHour->end_time;

        if ($startTime && $endTime) {
            return $pickupTime >= $startTime && $pickupTime <= $endTime;
        }

        return true;
    }

    /**
     * Check if the trip distance is within the operator's trip range.
     *
     * Compares the distance against the operator's dropoff_range_miles.
     * The trip range defines how far from their base the operator is willing to go.
     */
    protected function withinTripRange(Operator $operator, float $distanceMiles): bool
    {
        $tripRange = TripRange::where('operator_id', $operator->id)->first();

        // No trip range configured = no restriction
        if (!$tripRange) {
            return true;
        }

        return $distanceMiles <= $tripRange->dropoff_range_miles;
    }

    /**
     * Check if the operator has an active pause covering this fleet type at pickup time.
     *
     * Pauses can be:
     * - For all fleet types (all_fleet_types = true)
     * - For specific fleet types (fleet_type_ids JSON array contains the fleet type ID)
     *
     * Returns true if paused (meaning NOT available).
     */
    protected function isPaused(Operator $operator, int $fleetTypeId, Carbon $pickupDatetime): bool
    {
        $pauses = AvailabilityPause::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', $pickupDatetime)
            ->where('ends_at', '>=', $pickupDatetime)
            ->get();

        foreach ($pauses as $pause) {
            // If pause covers all fleet types, operator is paused
            if ($pause->all_fleet_types) {
                return true;
            }

            // If pause covers specific fleet types, check if ours is included
            $pausedFleetTypes = $pause->fleet_type_ids ?? [];
            if (in_array($fleetTypeId, $pausedFleetTypes)) {
                return true;
            }
        }

        return false;
    }
}
