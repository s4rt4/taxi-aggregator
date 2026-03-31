<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Pricing\PmpCalculator;
use App\Models\User;
use App\Models\Operator;
use App\Models\FleetType;
use App\Models\PerMilePrice;
use App\Models\PerMilePriceRange;
use App\Models\PerMileUplift;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PmpCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private PmpCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PmpCalculator();
    }

    // ---------------------------------------------------------------
    // Helper methods
    // ---------------------------------------------------------------

    /**
     * Create a User with the operator role.
     */
    private function createOperatorUser(): User
    {
        return User::factory()->create([
            'role' => 'operator',
        ]);
    }

    /**
     * Create an Operator linked to a new User.
     */
    private function createOperator(?User $user = null): Operator
    {
        $user = $user ?? $this->createOperatorUser();

        return Operator::create([
            'user_id' => $user->id,
            'operator_name' => 'Test Cabs',
            'email' => $user->email,
            'phone' => '07700900000',
            'postcode' => 'SW1A 1AA',
            'address_line_1' => '10 Downing Street',
            'city' => 'London',
            'licence_number' => 'PHV-TEST-001',
            'licence_authority' => 'Transport for London',
            'licence_expiry' => now()->addYear(),
            'status' => 'approved',
        ]);
    }

    /**
     * Create a FleetType with sensible defaults.
     */
    private function createFleetType(string $name = 'Standard', int $maxPassengers = 4): FleetType
    {
        return FleetType::create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)) . '-' . uniqid(),
            'max_passengers' => $maxPassengers,
            'is_active' => true,
        ]);
    }

    /**
     * Create a PerMilePrice record (the base PMP config).
     */
    private function createPmp(
        Operator $operator,
        FleetType $fleetType,
        float $ratePerMile = 2.00,
        float $minimumFare = 0.00,
        bool $isActive = true,
    ): PerMilePrice {
        return PerMilePrice::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'rate_per_mile' => $ratePerMile,
            'minimum_fare' => $minimumFare,
            'is_active' => $isActive,
        ]);
    }

    /**
     * Create a PerMilePriceRange (bracket override).
     */
    private function createRange(
        PerMilePrice $pmp,
        int $mileFrom,
        ?int $mileTo,
        float $ratePerMile,
    ): PerMilePriceRange {
        return PerMilePriceRange::create([
            'per_mile_price_id' => $pmp->id,
            'mile_from' => $mileFrom,
            'mile_to' => $mileTo,
            'rate_per_mile' => $ratePerMile,
        ]);
    }

    /**
     * Create a PerMileUplift (fleet-type uplift percentage for a distance band).
     */
    private function createUplift(
        Operator $operator,
        FleetType $fleetType,
        int $mileFrom,
        ?int $mileTo,
        float $upliftPercentage,
    ): PerMileUplift {
        return PerMileUplift::create([
            'operator_id' => $operator->id,
            'fleet_type_id' => $fleetType->id,
            'mile_from' => $mileFrom,
            'mile_to' => $mileTo,
            'uplift_percentage' => $upliftPercentage,
        ]);
    }

    // ---------------------------------------------------------------
    // Tests
    // ---------------------------------------------------------------

    public function test_returns_null_when_no_pmp_configured(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();

        $price = $this->calculator->calculate($operator->id, $fleetType->id, 10.0);

        $this->assertNull($price);
    }

    public function test_calculates_basic_price_using_base_rate(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $this->createPmp($operator, $fleetType, ratePerMile: 2.50);

        // 10 miles * 2.50 = 25.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 10.0);

        $this->assertNotNull($price);
        $this->assertEquals(25.00, $price);
    }

    public function test_uses_range_specific_rate_when_distance_falls_in_bracket(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $pmp = $this->createPmp($operator, $fleetType, ratePerMile: 2.00);

        // Range: 0-5 miles at 3.00 per mile
        $this->createRange($pmp, mileFrom: 0, mileTo: 5, ratePerMile: 3.00);
        // Range: 5-10 miles at 2.50 per mile
        $this->createRange($pmp, mileFrom: 5, mileTo: 10, ratePerMile: 2.50);

        // 3 miles falls in 0-5 bracket: 3 * 3.00 = 9.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 3.0);
        $this->assertEquals(9.00, $price);

        // 7 miles falls in 5-10 bracket: 7 * 2.50 = 17.50
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 7.0);
        $this->assertEquals(17.50, $price);
    }

    public function test_falls_back_to_base_rate_when_no_matching_range(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $pmp = $this->createPmp($operator, $fleetType, ratePerMile: 1.50);

        // Only define a range for 0-5 miles
        $this->createRange($pmp, mileFrom: 0, mileTo: 5, ratePerMile: 3.00);

        // 12 miles is outside the 0-5 range, should use base rate 1.50
        // 12 * 1.50 = 18.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 12.0);
        $this->assertEquals(18.00, $price);
    }

    public function test_open_ended_range_matches_distances_beyond_mile_from(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $pmp = $this->createPmp($operator, $fleetType, ratePerMile: 2.00);

        // Open-ended range: 20+ miles at 1.00 per mile (mile_to = null)
        $this->createRange($pmp, mileFrom: 20, mileTo: null, ratePerMile: 1.00);

        // 50 miles falls in the open-ended range: 50 * 1.00 = 50.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 50.0);
        $this->assertEquals(50.00, $price);
    }

    public function test_applies_uplift_percentage_correctly(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType('7-Seater', 7);
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00);

        // 40% uplift for 7-seaters on 5-10 mile trips
        $this->createUplift($operator, $fleetType, mileFrom: 5, mileTo: 10, upliftPercentage: 40.00);

        // 8 miles * 2.00 = 16.00, then +40% = 22.40
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 8.0);
        $this->assertEquals(22.40, $price);
    }

    public function test_uplift_with_open_ended_distance_band(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType('MPV', 6);
        $this->createPmp($operator, $fleetType, ratePerMile: 1.80);

        // 25% uplift for 20+ mile trips (open-ended)
        $this->createUplift($operator, $fleetType, mileFrom: 20, mileTo: null, upliftPercentage: 25.00);

        // 30 miles * 1.80 = 54.00, then +25% = 67.50
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 30.0);
        $this->assertEquals(67.50, $price);
    }

    public function test_no_uplift_when_distance_outside_uplift_band(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType('7-Seater', 7);
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00);

        // 40% uplift only for 5-10 mile trips
        $this->createUplift($operator, $fleetType, mileFrom: 5, mileTo: 10, upliftPercentage: 40.00);

        // 3 miles is outside the uplift band, no uplift applied
        // 3 * 2.00 = 6.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 3.0);
        $this->assertEquals(6.00, $price);
    }

    public function test_enforces_minimum_fare(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00, minimumFare: 10.00);

        // 2 miles * 2.00 = 4.00, but minimum is 10.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 2.0);
        $this->assertEquals(10.00, $price);
    }

    public function test_calculated_price_used_when_above_minimum_fare(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00, minimumFare: 10.00);

        // 20 miles * 2.00 = 40.00, which is above the minimum 10.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 20.0);
        $this->assertEquals(40.00, $price);
    }

    public function test_minimum_fare_applies_after_uplift(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType('8-Seater', 8);
        $this->createPmp($operator, $fleetType, ratePerMile: 1.00, minimumFare: 15.00);

        // 10% uplift for 0-5 mile trips
        $this->createUplift($operator, $fleetType, mileFrom: 0, mileTo: 5, upliftPercentage: 10.00);

        // 2 miles * 1.00 = 2.00, then +10% = 2.20, minimum is 15.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 2.0);
        $this->assertEquals(15.00, $price);
    }

    public function test_returns_null_for_inactive_pmp_records(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00, isActive: false);

        $price = $this->calculator->calculate($operator->id, $fleetType->id, 10.0);

        $this->assertNull($price);
    }

    public function test_calculate_all_returns_prices_for_all_fleet_types(): void
    {
        $operator = $this->createOperator();
        $standard = $this->createFleetType('Standard', 4);
        $estate = $this->createFleetType('Estate', 4);
        $mpv = $this->createFleetType('MPV', 6);

        $this->createPmp($operator, $standard, ratePerMile: 2.00);
        $this->createPmp($operator, $estate, ratePerMile: 2.50);
        $this->createPmp($operator, $mpv, ratePerMile: 3.00);

        $prices = $this->calculator->calculateAll($operator->id, 10.0);

        $this->assertCount(3, $prices);
        $this->assertEquals(20.00, $prices[$standard->id]); // 10 * 2.00
        $this->assertEquals(25.00, $prices[$estate->id]);    // 10 * 2.50
        $this->assertEquals(30.00, $prices[$mpv->id]);       // 10 * 3.00
    }

    public function test_calculate_all_excludes_inactive_fleet_types(): void
    {
        $operator = $this->createOperator();
        $standard = $this->createFleetType('Standard', 4);
        $estate = $this->createFleetType('Estate', 4);

        $this->createPmp($operator, $standard, ratePerMile: 2.00, isActive: true);
        $this->createPmp($operator, $estate, ratePerMile: 2.50, isActive: false);

        $prices = $this->calculator->calculateAll($operator->id, 10.0);

        $this->assertCount(1, $prices);
        $this->assertArrayHasKey($standard->id, $prices);
        $this->assertArrayNotHasKey($estate->id, $prices);
    }

    public function test_calculate_all_returns_empty_array_when_no_pmps(): void
    {
        $operator = $this->createOperator();

        $prices = $this->calculator->calculateAll($operator->id, 10.0);

        $this->assertIsArray($prices);
        $this->assertEmpty($prices);
    }

    public function test_combined_range_rate_and_uplift_and_minimum_fare(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType('7-Seater', 7);
        $pmp = $this->createPmp($operator, $fleetType, ratePerMile: 2.00, minimumFare: 5.00);

        // Range: 5-10 miles at 1.80 per mile
        $this->createRange($pmp, mileFrom: 5, mileTo: 10, ratePerMile: 1.80);

        // 40% uplift for 7-seaters on 5-10 mile trips
        $this->createUplift($operator, $fleetType, mileFrom: 5, mileTo: 10, upliftPercentage: 40.00);

        // 7 miles * 1.80 (range rate) = 12.60, then +40% uplift = 17.64
        // 17.64 > 5.00 minimum, so final price = 17.64
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 7.0);
        $this->assertEquals(17.64, $price);
    }

    public function test_price_rounds_to_two_decimal_places(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();
        $this->createPmp($operator, $fleetType, ratePerMile: 1.33);

        // 3 miles * 1.33 = 3.99
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 3.0);
        $this->assertEquals(3.99, $price);

        // 7 miles * 1.33 = 9.31
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 7.0);
        $this->assertEquals(9.31, $price);
    }

    public function test_zero_distance_returns_minimum_fare_or_zero(): void
    {
        $operator = $this->createOperator();
        $fleetType = $this->createFleetType();

        // With minimum fare
        $this->createPmp($operator, $fleetType, ratePerMile: 2.00, minimumFare: 5.00);

        // 0 miles * 2.00 = 0.00, minimum is 5.00
        $price = $this->calculator->calculate($operator->id, $fleetType->id, 0.0);
        $this->assertEquals(5.00, $price);
    }

    public function test_different_operators_have_independent_pricing(): void
    {
        $operator1 = $this->createOperator();
        $operator2 = $this->createOperator();
        $fleetType = $this->createFleetType();

        $this->createPmp($operator1, $fleetType, ratePerMile: 2.00);
        $this->createPmp($operator2, $fleetType, ratePerMile: 3.00);

        $price1 = $this->calculator->calculate($operator1->id, $fleetType->id, 10.0);
        $price2 = $this->calculator->calculate($operator2->id, $fleetType->id, 10.0);

        $this->assertEquals(20.00, $price1); // 10 * 2.00
        $this->assertEquals(30.00, $price2); // 10 * 3.00
    }
}
