<?php

namespace Tests\Unit;

use App\Models\AvailabilityPause;
use App\Models\DeadLegDiscount;
use App\Models\FlashSale;
use App\Models\FleetType;
use App\Models\LocationPrice;
use App\Models\MeetGreetCharge;
use App\Models\MeetGreetLocation;
use App\Models\NoticePeriod;
use App\Models\OperatingHour;
use App\Models\Operator;
use App\Models\PerMilePrice;
use App\Models\PostcodeAreaPrice;
use App\Models\Quote;
use App\Models\QuoteSearch;
use App\Models\TripRange;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAvailability;
use App\Services\Pricing\AvailabilityChecker;
use App\Services\Pricing\LpCalculator;
use App\Services\Pricing\PapCalculator;
use App\Services\Pricing\PmpCalculator;
use App\Services\Pricing\QuoteService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuoteService $quoteService;
    protected Operator $operator;
    protected FleetType $fleetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quoteService = new QuoteService(
            new PmpCalculator(),
            new LpCalculator(),
            new PapCalculator(),
            new AvailabilityChecker()
        );

        // Create a base user and operator
        $user = User::factory()->create(['role' => 'operator']);

        $this->operator = Operator::create([
            'user_id' => $user->id,
            'operator_name' => 'Test Cabs',
            'email' => 'test@testcabs.co.uk',
            'phone' => '02012345678',
            'postcode' => 'TW1 1AA',
            'address_line_1' => '1 Test Street',
            'city' => 'London',
            'licence_number' => 'PHO-12345',
            'licence_authority' => 'Transport for London',
            'licence_expiry' => now()->addYear(),
            'status' => 'approved',
            'approved_at' => now(),
            'commission_rate' => 12.00,
            'base_lat' => 51.4500,
            'base_lng' => -0.3250,
        ]);

        $this->fleetType = FleetType::create([
            'name' => 'Saloon',
            'slug' => 'saloon',
            'min_passengers' => 1,
            'max_passengers' => 4,
            'fuel_category' => 'standard',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create a vehicle so the operator has this fleet type
        Vehicle::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'make' => 'Toyota',
            'model' => 'Prius',
            'colour' => 'Black',
            'year' => 2023,
            'registration_plate' => 'AB12 CDE',
            'max_passengers' => 4,
            'max_luggage' => 2,
            'is_active' => true,
        ]);

        // Create vehicle availability (available every day)
        VehicleAvailability::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'day_of_week' => 'mon',
            'max_vehicles' => 5,
            'same_every_day' => true,
        ]);
    }

    // -------------------------------------------------------
    // calculatePrice: PAP > LP > PMP priority tests
    // -------------------------------------------------------

    public function test_calculate_price_returns_pap_when_available(): void
    {
        // Set up PAP pricing (highest priority)
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'from_postcode_area' => 'TW',
            'to_postcode_area' => 'WC',
            'price' => 45.00,
            'is_active' => true,
        ]);

        // Also set up LP and PMP (should be ignored)
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'start_postcode' => 'TW1',
            'start_radius_miles' => 5,
            'finish_postcode' => 'WC1',
            'finish_radius_miles' => 5,
            'price' => 50.00,
            'is_active' => true,
        ]);

        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.50,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        $result = $this->quoteService->calculatePrice(
            $this->operator->id,
            $this->fleetType->id,
            'TW1 1AA',
            'WC1A 1AA',
            15.0
        );

        $this->assertNotNull($result);
        $this->assertEquals(45.00, $result['price']);
        $this->assertEquals('pap', $result['source']);
    }

    public function test_calculate_price_falls_back_to_lp_when_no_pap(): void
    {
        // Set up LP pricing only (no PAP)
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'start_postcode' => 'TW1',
            'start_radius_miles' => 5,
            'finish_postcode' => 'WC1',
            'finish_radius_miles' => 5,
            'price' => 50.00,
            'is_active' => true,
        ]);

        // Also set up PMP (should be ignored)
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.50,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        $result = $this->quoteService->calculatePrice(
            $this->operator->id,
            $this->fleetType->id,
            'TW1 1AA',
            'WC1 2AB',
            15.0
        );

        $this->assertNotNull($result);
        $this->assertEquals(50.00, $result['price']);
        $this->assertEquals('lp', $result['source']);
    }

    public function test_calculate_price_falls_back_to_pmp_when_no_pap_or_lp(): void
    {
        // Set up PMP pricing only
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.50,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        $result = $this->quoteService->calculatePrice(
            $this->operator->id,
            $this->fleetType->id,
            'TW1 1AA',
            'WC1 2AB',
            15.0
        );

        $this->assertNotNull($result);
        $this->assertEquals(37.50, $result['price']); // 15 miles * 2.50
        $this->assertEquals('pmp', $result['source']);
    }

    public function test_calculate_price_returns_null_when_nothing_configured(): void
    {
        // No pricing configured at all
        $result = $this->quoteService->calculatePrice(
            $this->operator->id,
            $this->fleetType->id,
            'TW1 1AA',
            'WC1 2AB',
            15.0
        );

        $this->assertNull($result);
    }

    // -------------------------------------------------------
    // generateQuotes: Full integration tests
    // -------------------------------------------------------

    public function test_generate_quotes_creates_quote_search_and_quote_records(): void
    {
        // Set up PMP pricing for the operator
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        // QuoteSearch record should exist
        $this->assertInstanceOf(QuoteSearch::class, $quoteSearch);
        $this->assertDatabaseHas('quote_searches', [
            'id' => $quoteSearch->id,
            'pickup_address' => '1 High Street, Twickenham',
            'destination_address' => '1 Strand, London',
            'distance_miles' => 12.50,
        ]);

        // Should have created at least one quote
        $this->assertTrue($quoteSearch->quotes->count() > 0);

        $quote = $quoteSearch->quotes->first();
        $this->assertEquals($this->operator->id, $quote->operator_id);
        $this->assertEquals($this->fleetType->id, $quote->fleet_type_id);
        $this->assertEquals('pmp', $quote->price_source);
        $this->assertEquals(25.00, (float) $quote->base_price); // 12.50 * 2.00
        $this->assertEquals('Test Cabs', $quote->operator_name);
        $this->assertEquals('Saloon', $quote->fleet_type_name);
        $this->assertTrue($quote->is_available);
    }

    public function test_generate_quotes_skips_operators_outside_trip_range(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Set trip range to 10 miles (less than 12.5 mile search)
        TripRange::create([
            'operator_id' => $this->operator->id,
            'pickup_range_miles' => 10,
            'dropoff_range_miles' => 10,
        ]);

        $searchData = $this->makeSearchData(['distance_miles' => 12.50]);

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        // No quotes should be generated (operator is out of range)
        $this->assertEquals(0, $quoteSearch->quotes->count());
    }

    public function test_generate_quotes_skips_paused_operators(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Pause the operator for all fleet types, covering the pickup time
        AvailabilityPause::create([
            'operator_id' => $this->operator->id,
            'pause_type' => 'scheduled',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(10),
            'all_fleet_types' => true,
            'is_active' => true,
        ]);

        $searchData = $this->makeSearchData([
            'pickup_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $this->assertEquals(0, $quoteSearch->quotes->count());
    }

    // -------------------------------------------------------
    // Flash sale discount tests
    // -------------------------------------------------------

    public function test_flash_sale_percentage_discount_is_applied_correctly(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Create a flash sale: 20% off all fleet types
        FlashSale::create([
            'operator_id' => $this->operator->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(7),
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'all_fleet_types' => true,
            'all_routes' => true,
            'status' => 'active',
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $this->assertTrue($quoteSearch->quotes->count() > 0);

        $quote = $quoteSearch->quotes->first();
        $basePrice = (float) $quote->base_price;         // 12.50 * 2.00 = 25.00
        $flashDiscount = (float) $quote->flash_sale_discount;

        $this->assertEquals(25.00, $basePrice);
        $this->assertEquals(5.00, $flashDiscount);        // 20% of 25.00
        $this->assertEquals(20.00, (float) $quote->total_price); // 25.00 - 5.00
        $this->assertNotNull($quote->flash_sale_id);
    }

    public function test_flash_sale_fixed_discount_is_applied_correctly(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Create a fixed flash sale: GBP 8.00 off
        FlashSale::create([
            'operator_id' => $this->operator->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(7),
            'discount_type' => 'fixed',
            'discount_value' => 8.00,
            'all_fleet_types' => true,
            'all_routes' => true,
            'status' => 'active',
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $quote = $quoteSearch->quotes->first();
        $this->assertEquals(8.00, (float) $quote->flash_sale_discount);
        $this->assertEquals(17.00, (float) $quote->total_price); // 25.00 - 8.00
    }

    // -------------------------------------------------------
    // Meet & greet charge tests
    // -------------------------------------------------------

    public function test_meet_greet_charge_is_added_to_quote(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Create a meet & greet location and charge
        $location = MeetGreetLocation::create([
            'name' => 'Heathrow Airport',
            'type' => 'airport',
            'code' => 'LHR',
            'lat' => 51.4700,
            'lng' => -0.4543,
            'is_active' => true,
        ]);

        MeetGreetCharge::create([
            'operator_id' => $this->operator->id,
            'meet_greet_location_id' => $location->id,
            'charge' => 7.50,
            'is_active' => true,
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $quote = $quoteSearch->quotes->first();
        $this->assertEquals(7.50, (float) $quote->meet_greet_charge);
        $this->assertTrue($quote->meet_and_greet);
        // Total = base (25.00) + meet_greet (7.50)
        $this->assertEquals(32.50, (float) $quote->total_price);
    }

    // -------------------------------------------------------
    // Dead leg discount tests
    // -------------------------------------------------------

    public function test_dead_leg_discount_is_applied_correctly(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Create a dead leg discount: 15% off for TW -> WC
        DeadLegDiscount::create([
            'operator_id' => $this->operator->id,
            'from_area' => 'TW',
            'to_area' => 'WC',
            'available_from' => now()->subHour(),
            'available_until' => now()->addDays(7),
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'status' => 'active',
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $quote = $quoteSearch->quotes->first();
        $basePrice = (float) $quote->base_price;
        $deadLegDiscount = (float) $quote->dead_leg_discount;

        $this->assertEquals(25.00, $basePrice);
        $this->assertEquals(3.75, $deadLegDiscount); // 15% of 25.00
        $this->assertEquals(21.25, (float) $quote->total_price); // 25.00 - 3.75
        $this->assertNotNull($quote->dead_leg_discount_id);
    }

    // -------------------------------------------------------
    // Quotes are ordered by price
    // -------------------------------------------------------

    public function test_quotes_are_returned_ordered_by_total_price(): void
    {
        // Create a second fleet type with higher pricing
        $executiveFleetType = FleetType::create([
            'name' => 'Executive',
            'slug' => 'executive',
            'min_passengers' => 1,
            'max_passengers' => 3,
            'fuel_category' => 'standard',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Vehicle::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $executiveFleetType->id,
            'make' => 'Mercedes',
            'model' => 'E-Class',
            'colour' => 'Black',
            'year' => 2023,
            'registration_plate' => 'XY67 FGH',
            'max_passengers' => 3,
            'max_luggage' => 3,
            'is_active' => true,
        ]);

        VehicleAvailability::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $executiveFleetType->id,
            'day_of_week' => 'mon',
            'max_vehicles' => 3,
            'same_every_day' => true,
        ]);

        // Saloon at GBP 2/mile, Executive at GBP 4/mile
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $executiveFleetType->id,
            'rate_per_mile' => 4.00,
            'minimum_fare' => 20.00,
            'is_active' => true,
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $this->assertEquals(2, $quoteSearch->quotes->count());

        // Should be ordered: Saloon (25.00) then Executive (50.00)
        $quotes = $quoteSearch->quotes;
        $this->assertEquals('Saloon', $quotes[0]->fleet_type_name);
        $this->assertEquals(25.00, (float) $quotes[0]->total_price);
        $this->assertEquals('Executive', $quotes[1]->fleet_type_name);
        $this->assertEquals(50.00, (float) $quotes[1]->total_price);
    }

    // -------------------------------------------------------
    // Combined discounts test
    // -------------------------------------------------------

    public function test_flash_sale_and_dead_leg_discount_stack(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 4.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Flash sale: 10% off
        FlashSale::create([
            'operator_id' => $this->operator->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(7),
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'all_fleet_types' => true,
            'all_routes' => true,
            'status' => 'active',
        ]);

        // Dead leg: GBP 5 off
        DeadLegDiscount::create([
            'operator_id' => $this->operator->id,
            'from_area' => 'TW',
            'to_area' => 'WC',
            'available_from' => now()->subHour(),
            'available_until' => now()->addDays(7),
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
            'status' => 'active',
        ]);

        $searchData = $this->makeSearchData();

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $quote = $quoteSearch->quotes->first();
        $basePrice = (float) $quote->base_price; // 12.50 * 4.00 = 50.00

        $this->assertEquals(50.00, $basePrice);
        $this->assertEquals(5.00, (float) $quote->flash_sale_discount);  // 10% of 50
        $this->assertEquals(5.00, (float) $quote->dead_leg_discount);    // fixed 5.00
        // Total: 50.00 - 5.00 - 5.00 = 40.00
        $this->assertEquals(40.00, (float) $quote->total_price);
    }

    // -------------------------------------------------------
    // AvailabilityChecker tests
    // -------------------------------------------------------

    public function test_notice_period_blocks_too_short_bookings(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Require 24 hours notice
        NoticePeriod::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'hours_notice' => 24,
        ]);

        // Pickup in 2 hours (less than 24 hour notice)
        $searchData = $this->makeSearchData([
            'pickup_datetime' => now()->addHours(2)->format('Y-m-d H:i:s'),
        ]);

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $this->assertEquals(0, $quoteSearch->quotes->count());
    }

    public function test_operating_hours_blocks_out_of_hours_pickups(): void
    {
        // Set up PMP pricing
        PerMilePrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'rate_per_mile' => 2.00,
            'minimum_fare' => 10.00,
            'is_active' => true,
        ]);

        // Operating hours: 9am - 6pm
        OperatingHour::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->fleetType->id,
            'is_24_hours' => false,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'excluded_days' => [],
        ]);

        // Pickup at 11pm (out of hours)
        $pickupTime = Carbon::tomorrow()->setHour(23)->setMinute(0)->setSecond(0);

        $searchData = $this->makeSearchData([
            'pickup_datetime' => $pickupTime->format('Y-m-d H:i:s'),
        ]);

        $quoteSearch = $this->quoteService->generateQuotes($searchData);

        $this->assertEquals(0, $quoteSearch->quotes->count());
    }

    // -------------------------------------------------------
    // Helper to build standard search data
    // -------------------------------------------------------

    protected function makeSearchData(array $overrides = []): array
    {
        return array_merge([
            'pickup_address' => '1 High Street, Twickenham',
            'pickup_postcode' => 'TW1 1AA',
            'pickup_lat' => 51.4492,
            'pickup_lng' => -0.3254,
            'destination_address' => '1 Strand, London',
            'destination_postcode' => 'WC2N 5HR',
            'destination_lat' => 51.5074,
            'destination_lng' => -0.1278,
            'pickup_datetime' => now()->addDays(3)->setHour(14)->setMinute(0)->format('Y-m-d H:i:s'),
            'passenger_count' => 2,
            'luggage_count' => 1,
            'distance_miles' => 12.50,
            'estimated_duration_minutes' => 45,
            'user_id' => null,
            'session_id' => 'test-session-123',
            'ip_address' => '127.0.0.1',
        ], $overrides);
    }
}
