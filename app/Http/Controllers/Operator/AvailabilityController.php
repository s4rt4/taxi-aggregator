<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityPause;
use App\Models\FleetType;
use App\Models\NoticePeriod;
use App\Models\OperatingHour;
use App\Models\PostcodeLeadTime;
use App\Models\TripRange;
use App\Models\VehicleAvailability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Get the authenticated user's operator.
     */
    protected function getOperator()
    {
        return auth()->user()->operator;
    }

    // =========================================================================
    // Number of Vehicles
    // =========================================================================

    /**
     * Show the vehicle availability page.
     */
    public function vehicles()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $availability = $operator?->vehicleAvailability()->get() ?? collect();

        return view('operator.availability.vehicles', compact('fleetTypes', 'availability'));
    }

    /**
     * Save vehicle availability across fleet types and days of the week.
     */
    public function saveVehicles(Request $request)
    {
        $request->validate([
            'vehicles' => ['nullable', 'array'],
            'vehicles.*' => ['nullable', 'array'],
            'vehicles.*.*.max_vehicles' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'same_every_day' => ['nullable', 'array'],
            'same_every_day.*' => ['nullable', 'boolean'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Days of the week matching the database enum
        $daysOfWeek = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        foreach ($request->input('vehicles', []) as $fleetTypeId => $dayData) {
            $sameEveryDay = !empty($request->input("same_every_day.{$fleetTypeId}"));

            foreach ($daysOfWeek as $index => $day) {
                $maxVehicles = $dayData[$day]['max_vehicles'] ?? ($dayData[$index]['max_vehicles'] ?? null);

                // If same_every_day, use Monday's value for all days
                if ($sameEveryDay && $index > 0) {
                    $maxVehicles = $dayData['mon']['max_vehicles'] ?? ($dayData[0]['max_vehicles'] ?? null);
                }

                if ($maxVehicles !== null && $maxVehicles !== '') {
                    VehicleAvailability::updateOrCreate(
                        [
                            'operator_id' => $operator->id,
                            'fleet_type_id' => $fleetTypeId,
                            'day_of_week' => $day,
                        ],
                        [
                            'max_vehicles' => (int) $maxVehicles,
                            'same_every_day' => $sameEveryDay,
                        ]
                    );
                } else {
                    // Remove record if no value set
                    $operator->vehicleAvailability()
                        ->where('fleet_type_id', $fleetTypeId)
                        ->where('day_of_week', $day)
                        ->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Vehicle availability saved successfully.');
    }

    // =========================================================================
    // Notice Periods
    // =========================================================================

    /**
     * Show the notice periods page.
     */
    public function notice()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $noticePeriods = $operator?->noticePeriods()->get()->keyBy('fleet_type_id') ?? collect();
        $leadTimes = $operator?->postcodeLeadTimes()->get() ?? collect();

        return view('operator.availability.notice', compact('fleetTypes', 'noticePeriods', 'leadTimes'));
    }

    /**
     * Save notice period settings per fleet type and postcode lead times.
     */
    public function saveNotice(Request $request)
    {
        $request->validate([
            'notice_periods' => ['nullable', 'array'],
            'notice_periods.*' => ['nullable', 'integer', 'min:0', 'max:720'],
            'lead_times' => ['nullable', 'array'],
            'lead_times.*.postcode_area' => ['required_with:lead_times.*.notice_value', 'nullable', 'string', 'max:10'],
            'lead_times.*.notice_type' => ['nullable', 'in:hours,days'],
            'lead_times.*.notice_value' => ['nullable', 'integer', 'min:0', 'max:720'],
            'lead_times.*.fleet_type_ids' => ['nullable', 'array'],
            'lead_times.*.fleet_type_ids.*' => ['exists:fleet_types,id'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        // Upsert notice periods per fleet type
        foreach ($request->input('notice_periods', []) as $fleetTypeId => $hoursNotice) {
            if ($hoursNotice !== null && $hoursNotice !== '') {
                NoticePeriod::updateOrCreate(
                    [
                        'operator_id' => $operator->id,
                        'fleet_type_id' => $fleetTypeId,
                    ],
                    [
                        'hours_notice' => (int) $hoursNotice,
                    ]
                );
            } else {
                $operator->noticePeriods()
                    ->where('fleet_type_id', $fleetTypeId)
                    ->delete();
            }
        }

        // Replace postcode lead times
        $operator->postcodeLeadTimes()->delete();

        foreach ($request->input('lead_times', []) as $leadTimeData) {
            if (!empty($leadTimeData['postcode_area']) && isset($leadTimeData['notice_value'])) {
                PostcodeLeadTime::create([
                    'operator_id' => $operator->id,
                    'postcode_area' => strtoupper(trim($leadTimeData['postcode_area'])),
                    'notice_type' => $leadTimeData['notice_type'] ?? 'hours',
                    'notice_value' => (int) $leadTimeData['notice_value'],
                    'fleet_type_ids' => $leadTimeData['fleet_type_ids'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Notice periods saved successfully.');
    }

    // =========================================================================
    // Trip Range
    // =========================================================================

    /**
     * Show the trip range settings page.
     */
    public function tripRange()
    {
        $operator = $this->getOperator();
        $tripRange = $operator?->tripRange;

        return view('operator.availability.trip-range', compact('tripRange'));
    }

    /**
     * Save trip range settings.
     */
    public function saveTripRange(Request $request)
    {
        $validated = $request->validate([
            'pickup_range_miles' => ['required', 'integer', 'min:1', 'max:500'],
            'dropoff_range_miles' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        TripRange::updateOrCreate(
            ['operator_id' => $operator->id],
            [
                'pickup_range_miles' => $validated['pickup_range_miles'],
                'dropoff_range_miles' => $validated['dropoff_range_miles'],
            ]
        );

        return redirect()->back()->with('success', 'Trip range saved successfully.');
    }

    // =========================================================================
    // Operating Hours
    // =========================================================================

    /**
     * Show the operating hours page.
     */
    public function hours()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $hours = $operator?->operatingHours()->get()->keyBy('fleet_type_id') ?? collect();

        return view('operator.availability.hours', compact('fleetTypes', 'hours'));
    }

    /**
     * Save operating hours per fleet type.
     */
    public function saveHours(Request $request)
    {
        $request->validate([
            'hours' => ['nullable', 'array'],
            'hours.*.is_24_hours' => ['nullable', 'boolean'],
            'hours.*.start_time' => ['nullable', 'date_format:H:i'],
            'hours.*.end_time' => ['nullable', 'date_format:H:i'],
            'hours.*.excluded_days' => ['nullable', 'array'],
            'hours.*.excluded_days.*' => ['integer', 'min:0', 'max:6'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        foreach ($request->input('hours', []) as $fleetTypeId => $hourData) {
            $is24Hours = !empty($hourData['is_24_hours']);

            OperatingHour::updateOrCreate(
                [
                    'operator_id' => $operator->id,
                    'fleet_type_id' => $fleetTypeId,
                ],
                [
                    'is_24_hours' => $is24Hours,
                    'start_time' => $is24Hours ? null : ($hourData['start_time'] ?? null),
                    'end_time' => $is24Hours ? null : ($hourData['end_time'] ?? null),
                    'excluded_days' => $hourData['excluded_days'] ?? [],
                ]
            );
        }

        return redirect()->back()->with('success', 'Operating hours saved successfully.');
    }

    // =========================================================================
    // Pause Availability
    // =========================================================================

    /**
     * Show the pause availability page.
     */
    public function pause()
    {
        $operator = $this->getOperator();
        $fleetTypes = FleetType::active()->ordered()->get();
        $activePauses = $operator?->availabilityPauses()->where('is_active', true)->get() ?? collect();

        return view('operator.availability.pause', compact('fleetTypes', 'activePauses'));
    }

    /**
     * Create an immediate availability pause.
     */
    public function storeImmediatePause(Request $request)
    {
        $validated = $request->validate([
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:43200'],
            'fleet_type_ids' => ['nullable', 'array'],
            'fleet_type_ids.*' => ['exists:fleet_types,id'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        $allFleetTypes = empty($validated['fleet_type_ids']);
        $startsAt = now();
        $endsAt = now()->addMinutes($validated['duration_minutes']);

        AvailabilityPause::create([
            'operator_id' => $operator->id,
            'pause_type' => 'immediate',
            'duration_minutes' => $validated['duration_minutes'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'all_fleet_types' => $allFleetTypes,
            'fleet_type_ids' => $allFleetTypes ? null : $validated['fleet_type_ids'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Availability paused immediately for ' . $validated['duration_minutes'] . ' minutes.');
    }

    /**
     * Create a future (scheduled) availability pause.
     */
    public function storeFuturePause(Request $request)
    {
        $validated = $request->validate([
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:43200'],
            'starts_at' => ['required', 'date', 'after:now'],
            'fleet_type_ids' => ['nullable', 'array'],
            'fleet_type_ids.*' => ['exists:fleet_types,id'],
        ]);

        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->back()->with('error', 'Please complete your operator profile first.');
        }

        $allFleetTypes = empty($validated['fleet_type_ids']);
        $startsAt = \Carbon\Carbon::parse($validated['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($validated['duration_minutes']);

        AvailabilityPause::create([
            'operator_id' => $operator->id,
            'pause_type' => 'scheduled',
            'duration_minutes' => $validated['duration_minutes'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'all_fleet_types' => $allFleetTypes,
            'fleet_type_ids' => $allFleetTypes ? null : $validated['fleet_type_ids'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Scheduled availability pause created successfully.');
    }
}
