<?php

namespace Tests\Traits;

use App\Models\Booking;
use App\Models\FleetType;
use App\Models\NoticePeriod;
use App\Models\OperatingHour;
use App\Models\Operator;
use App\Models\PerMilePrice;
use App\Models\TripRange;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAvailability;

trait CreatesTestData
{
    protected function createPassenger(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'passenger',
            'is_active' => true,
        ], $attrs));
    }

    protected function createOperatorUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'operator',
            'is_active' => true,
        ], $attrs));
    }

    protected function createAdmin(array $attrs = []): User
    {
        $role = \App\Models\AdminRole::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'is_system' => true, 'permissions' => null]
        );

        return User::factory()->create(array_merge([
            'role' => 'admin',
            'is_active' => true,
            'admin_role_id' => $role->id,
        ], $attrs));
    }

    protected function createApprovedOperator(?User $user = null): Operator
    {
        $user = $user ?? $this->createOperatorUser();

        return Operator::create([
            'user_id' => $user->id,
            'operator_name' => 'Test Cabs Ltd',
            'legal_company_name' => 'Test Cabs Limited',
            'email' => $user->email,
            'phone' => '020 7946 0958',
            'postcode' => 'SW1A 1AA',
            'address_line_1' => '10 Downing Street',
            'city' => 'London',
            'county' => 'Greater London',
            'licence_number' => 'PHO-12345',
            'licence_authority' => 'Transport for London',
            'licence_expiry' => now()->addYear(),
            'fleet_size' => 10,
            'status' => 'approved',
            'approved_at' => now(),
            'commission_rate' => 12.00,
            'tier' => 'basic',
        ]);
    }

    protected function createFleetTypeWithPricing(Operator $operator): FleetType
    {
        $fleetType = FleetType::create([
            'name' => 'Standard',
            'slug' => 'standard-' . $operator->id,
            'min_passengers' => 1,
            'max_passengers' => 4,
            'fuel_category' => 'petrol_diesel_hybrid',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create a vehicle so the operator is linked to this fleet type
        Vehicle::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'make' => 'Toyota',
            'model' => 'Prius',
            'registration_plate' => 'AB12 CDE',
            'max_passengers' => 4,
            'max_luggage' => 2,
            'is_active' => true,
        ]);

        // PMP pricing
        PerMilePrice::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'rate_per_mile' => 2.50,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Trip range
        TripRange::create([
            'operator_id' => $operator->id,
            'pickup_range_miles' => 50,
            'dropoff_range_miles' => 200,
        ]);

        // Vehicle availability (mon-sun)
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($days as $index => $day) {
            VehicleAvailability::create([
                'operator_id' => $operator->id,
                'fleet_type_id' => $fleetType->id,
                'day_of_week' => $day,
                'max_vehicles' => 5,
                'same_every_day' => true,
            ]);
        }

        // Notice period
        NoticePeriod::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'hours_notice' => 2,
        ]);

        // Operating hours - 24/7
        OperatingHour::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'is_24_hours' => true,
            'excluded_days' => [],
        ]);

        return $fleetType;
    }

    protected function createBooking(User $passenger, Operator $operator, FleetType $fleetType): Booking
    {
        return Booking::create([
            'passenger_id' => $passenger->id,
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'pickup_address' => '10 Downing Street, London',
            'pickup_lat' => 51.5034,
            'pickup_lng' => -0.1276,
            'destination_address' => 'Heathrow Airport, London',
            'destination_lat' => 51.4700,
            'destination_lng' => -0.4543,
            'distance_miles' => 15.00,
            'estimated_duration_minutes' => 45,
            'pickup_datetime' => now()->addDay(),
            'passenger_name' => $passenger->name,
            'passenger_phone' => '07700 900000',
            'passenger_email' => $passenger->email,
            'passenger_count' => 2,
            'luggage_count' => 1,
            'price_source' => 'pmp',
            'base_price' => 37.50,
            'commission_rate' => 12.00,
            'commission_amount' => 4.50,
            'total_price' => 37.50,
            'payment_type' => 'prepaid',
            'status' => 'pending',
        ]);
    }
}
