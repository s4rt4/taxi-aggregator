<?php

namespace Tests\Unit;

use App\Models\FleetType;
use App\Models\LocationPrice;
use App\Models\Operator;
use App\Models\User;
use App\Services\Pricing\LpCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LpCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private LpCalculator $calculator;
    private Operator $operator;
    private FleetType $standardFleet;
    private FleetType $mpvFleet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new LpCalculator();

        $user = User::factory()->create(['role' => 'operator']);

        $this->operator = Operator::create([
            'user_id' => $user->id,
            'operator_name' => 'Test Cabs',
            'email' => 'test@testcabs.co.uk',
            'phone' => '07700900000',
            'postcode' => 'SO14 0AA',
            'address_line_1' => '1 Test Street',
            'city' => 'Southampton',
            'licence_number' => 'PHO/12345',
            'licence_authority' => 'Southampton City Council',
            'licence_expiry' => now()->addYear(),
            'status' => 'approved',
        ]);

        $this->standardFleet = FleetType::create([
            'name' => 'Standard',
            'slug' => 'standard',
            'max_passengers' => 4,
            'sort_order' => 1,
        ]);

        $this->mpvFleet = FleetType::create([
            'name' => 'MPV',
            'slug' => 'mpv',
            'max_passengers' => 6,
            'sort_order' => 2,
        ]);
    }

    // -------------------------------------------------------
    // extractPostcodePrefix tests
    // -------------------------------------------------------

    public function test_extract_postcode_prefix_with_space(): void
    {
        $this->assertEquals('SO14', LpCalculator::extractPostcodePrefix('SO14 2AA'));
        $this->assertEquals('SW1A', LpCalculator::extractPostcodePrefix('SW1A 1AA'));
        $this->assertEquals('B1', LpCalculator::extractPostcodePrefix('B1 1AA'));
        $this->assertEquals('EC1A', LpCalculator::extractPostcodePrefix('EC1A 1BB'));
        $this->assertEquals('W1', LpCalculator::extractPostcodePrefix('W1 4AA'));
    }

    public function test_extract_postcode_prefix_without_space(): void
    {
        $this->assertEquals('SO14', LpCalculator::extractPostcodePrefix('SO142AA'));
        $this->assertEquals('SW1A', LpCalculator::extractPostcodePrefix('SW1A1AA'));
        $this->assertEquals('B1', LpCalculator::extractPostcodePrefix('B11AA'));
    }

    public function test_extract_postcode_prefix_normalizes_case(): void
    {
        $this->assertEquals('SO14', LpCalculator::extractPostcodePrefix('so14 2aa'));
        $this->assertEquals('SW1A', LpCalculator::extractPostcodePrefix('sw1a 1aa'));
    }

    public function test_extract_postcode_prefix_trims_whitespace(): void
    {
        $this->assertEquals('SO14', LpCalculator::extractPostcodePrefix('  SO14 2AA  '));
    }

    // -------------------------------------------------------
    // calculate() tests
    // -------------------------------------------------------

    public function test_returns_null_when_no_lp_configured(): void
    {
        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNull($result);
    }

    public function test_finds_exact_postcode_match(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(85.00, $result);
    }

    public function test_finds_reverse_direction_match_when_also_reverse_is_true(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => true,
            'is_active' => true,
        ]);

        // Reverse: pickup is SW1A, destination is SO14
        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SW1A 1AA',
            'SO14 2AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(85.00, $result);
    }

    public function test_does_not_find_reverse_when_also_reverse_is_false(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        // Reverse direction should NOT match
        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SW1A 1AA',
            'SO14 2AA'
        );

        $this->assertNull($result);
    }

    public function test_ignores_inactive_location_prices(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => false,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNull($result);
    }

    public function test_does_not_match_wrong_operator(): void
    {
        $otherUser = User::factory()->create(['role' => 'operator']);
        $otherOperator = Operator::create([
            'user_id' => $otherUser->id,
            'operator_name' => 'Other Cabs',
            'email' => 'other@cabs.co.uk',
            'phone' => '07700900001',
            'postcode' => 'W1 1AA',
            'address_line_1' => '2 Other Street',
            'city' => 'London',
            'licence_number' => 'PHO/99999',
            'licence_authority' => 'TfL',
            'licence_expiry' => now()->addYear(),
            'status' => 'approved',
        ]);

        LocationPrice::create([
            'operator_id' => $otherOperator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 99.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNull($result);
    }

    public function test_does_not_match_wrong_fleet_type(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 120.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNull($result);
    }

    public function test_matches_case_insensitively(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'so14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'sw1a',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(85.00, $result);
    }

    // -------------------------------------------------------
    // calculateAll() tests
    // -------------------------------------------------------

    public function test_calculate_all_returns_all_fleet_type_prices(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 120.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertCount(2, $results);
        $this->assertEquals(85.00, $results[$this->standardFleet->id]);
        $this->assertEquals(120.00, $results[$this->mpvFleet->id]);
    }

    public function test_calculate_all_returns_empty_array_when_no_matches(): void
    {
        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_calculate_all_excludes_inactive_prices(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 85.00,
            'also_reverse' => false,
            'is_active' => true,
        ]);

        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'start_postcode' => 'SO14',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SW1A',
            'finish_radius_miles' => 5,
            'price' => 120.00,
            'also_reverse' => false,
            'is_active' => false,
        ]);

        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertCount(1, $results);
        $this->assertEquals(85.00, $results[$this->standardFleet->id]);
        $this->assertArrayNotHasKey($this->mpvFleet->id, $results);
    }

    public function test_calculate_all_includes_reverse_matches(): void
    {
        LocationPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'start_postcode' => 'SW1A',
            'start_radius_miles' => 5,
            'finish_postcode' => 'SO14',
            'finish_radius_miles' => 5,
            'price' => 90.00,
            'also_reverse' => true,
            'is_active' => true,
        ]);

        // Querying SO14 -> SW1A should find the reverse match
        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertCount(1, $results);
        $this->assertEquals(90.00, $results[$this->standardFleet->id]);
    }
}
