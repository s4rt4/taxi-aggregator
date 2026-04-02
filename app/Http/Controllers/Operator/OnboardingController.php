<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\FleetType;
use App\Models\Operator;
use App\Models\PerMilePrice;
use App\Models\TripRange;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $operator = $user->operator;

        // Determine current step based on what's been completed
        $step = $this->getCurrentStep($operator);

        return redirect()->route('operator.onboarding.step', $step);
    }

    public function step(int $step)
    {
        $user = auth()->user();
        $operator = $user->operator;
        $fleetTypes = FleetType::active()->ordered()->get();
        $totalSteps = 5;

        if ($step < 1 || $step > 5) {
            return redirect()->route('operator.onboarding');
        }

        return view('operator.onboarding.step' . $step, compact('operator', 'fleetTypes', 'step', 'totalSteps'));
    }

    /**
     * Step 1: Company Details (name, legal name, email, phone)
     */
    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'business_type' => ['required', 'in:sole_trader,limited_company,partnership,llp'],
            'operator_name' => ['required', 'string', 'max:255'],
            'legal_company_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $operator = $this->getOrCreateOperator();

        $operator->update([
            'business_type' => $validated['business_type'],
            'operator_name' => $validated['operator_name'],
            'legal_company_name' => $validated['legal_company_name'] ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('operator.onboarding.step', 2);
    }

    /**
     * Step 2: Address & Location (postcode, address, city, county)
     */
    public function saveStep2(Request $request)
    {
        $validated = $request->validate([
            'postcode' => ['required', 'string', 'max:10'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'county' => ['nullable', 'string', 'max:255'],
        ]);

        $operator = $this->getOrCreateOperator();

        $operator->update($validated);

        return redirect()->route('operator.onboarding.step', 3);
    }

    /**
     * Step 3: Licence & Fleet (licence number, authority, expiry, fleet size)
     */
    public function saveStep3(Request $request)
    {
        $validated = $request->validate([
            'licence_number' => ['required', 'string', 'max:100'],
            'licence_authority' => ['required', 'string', 'max:255'],
            'licence_expiry' => ['required', 'date', 'after:today'],
            'fleet_size' => ['required', 'integer', 'min:1', 'max:9999'],
            'dispatch_system' => ['nullable', 'string', 'max:100'],
        ]);

        $operator = $this->getOrCreateOperator();

        $operator->update($validated);

        return redirect()->route('operator.onboarding.step', 4);
    }

    /**
     * Step 4: Pricing Setup (select fleet types, set basic PMP rates)
     */
    public function saveStep4(Request $request)
    {
        $validated = $request->validate([
            'fleet_types' => ['required', 'array', 'min:1'],
            'fleet_types.*' => ['integer', 'exists:fleet_types,id'],
            'rate_per_mile' => ['required', 'array'],
            'rate_per_mile.*' => ['nullable', 'numeric', 'min:0.01', 'max:999.99'],
            'minimum_fare' => ['required', 'array'],
            'minimum_fare.*' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        $operator = $this->getOrCreateOperator();

        // Remove old per-mile prices first
        $operator->perMilePrices()->delete();

        // Save new per-mile prices for selected fleet types
        foreach ($validated['fleet_types'] as $fleetTypeId) {
            $rate = $validated['rate_per_mile'][$fleetTypeId] ?? null;
            $minFare = $validated['minimum_fare'][$fleetTypeId] ?? null;

            if ($rate) {
                PerMilePrice::create([
                    'operator_id' => $operator->id,
                    'fleet_type_id' => $fleetTypeId,
                    'rate_per_mile' => $rate,
                    'minimum_fare' => $minFare ?? 0,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('operator.onboarding.step', 5);
    }

    /**
     * Step 5: Availability (trip range, vehicle count, notice period)
     */
    public function saveStep5(Request $request)
    {
        $validated = $request->validate([
            'pickup_range_miles' => ['required', 'integer', 'min:1', 'max:500'],
            'dropoff_range_miles' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $operator = $this->getOrCreateOperator();

        TripRange::updateOrCreate(
            ['operator_id' => $operator->id],
            [
                'pickup_range_miles' => $validated['pickup_range_miles'],
                'dropoff_range_miles' => $validated['dropoff_range_miles'],
            ]
        );

        return redirect()->route('operator.onboarding.complete')
            ->with('success', 'Your account setup is complete!');
    }

    public function complete()
    {
        $operator = auth()->user()->operator;

        return view('operator.onboarding.complete', compact('operator'));
    }

    protected function getCurrentStep(?Operator $operator): int
    {
        if (!$operator) return 1;
        if (!$operator->operator_name) return 1;
        if (!$operator->postcode) return 2;
        if (!$operator->licence_number) return 3;
        if ($operator->perMilePrices()->count() === 0) return 4;
        if (!$operator->tripRange) return 5;
        return 5; // all done
    }

    protected function getOrCreateOperator(): Operator
    {
        $user = auth()->user();

        if (!$user->operator) {
            $operator = Operator::create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);
            $user->load('operator');
            return $operator;
        }

        return $user->operator;
    }
}
