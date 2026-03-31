<?php

namespace Tests\Unit;

use App\Models\FleetType;
use App\Models\Operator;
use App\Models\PostcodeAreaPrice;
use App\Models\User;
use App\Services\Pricing\PapCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PapCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private PapCalculator $calculator;
    private Operator $operator;
    private FleetType $standardFleet;
    private FleetType $mpvFleet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new PapCalculator();

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
    // extractPostcodeArea tests
    // -------------------------------------------------------

    public function test_extracts_two_letter_area_from_postcode_with_space(): void
    {
        $this->assertEquals('SO', PapCalculator::extractPostcodeArea('SO14 2AA'));
    }

    public function test_extracts_two_letter_area_from_sw_postcode(): void
    {
        $this->assertEquals('SW', PapCalculator::extractPostcodeArea('SW1A 1AA'));
    }

    public function test_extracts_two_letter_area_from_ec_postcode(): void
    {
        $this->assertEquals('EC', PapCalculator::extractPostcodeArea('EC1A 1BB'));
    }

    public function test_extracts_single_letter_area_from_b_postcode(): void
    {
        $this->assertEquals('B', PapCalculator::extractPostcodeArea('B1 1AA'));
    }

    public function test_extracts_two_letter_area_from_ab_postcode(): void
    {
        $this->assertEquals('AB', PapCalculator::extractPostcodeArea('AB10 1CD'));
    }

    public function test_extracts_single_letter_area_from_w_postcode(): void
    {
        $this->assertEquals('W', PapCalculator::extractPostcodeArea('W1A 1AA'));
    }

    public function test_extract_area_normalizes_case(): void
    {
        $this->assertEquals('SO', PapCalculator::extractPostcodeArea('so14 2aa'));
        $this->assertEquals('SW', PapCalculator::extractPostcodeArea('sw1a 1aa'));
    }

    public function test_extract_area_trims_whitespace(): void
    {
        $this->assertEquals('SO', PapCalculator::extractPostcodeArea('  SO14 2AA  '));
    }

    public function test_extract_area_handles_no_space_postcode(): void
    {
        $this->assertEquals('SO', PapCalculator::extractPostcodeArea('SO142AA'));
        $this->assertEquals('B', PapCalculator::extractPostcodeArea('B11AA'));
    }

    // -------------------------------------------------------
    // calculate() tests
    // -------------------------------------------------------

    public function test_returns_null_when_no_pap_configured(): void
    {
        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNull($result);
    }

    public function test_finds_exact_area_match(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 45.00,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(45.00, $result);
    }

    public function test_matches_single_letter_areas(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'B',
            'to_postcode_area' => 'W',
            'price' => 55.00,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'B1 1AA',
            'W1A 1AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(55.00, $result);
    }

    public function test_ignores_inactive_paps(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 45.00,
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

        PostcodeAreaPrice::create([
            'operator_id' => $otherOperator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 99.00,
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
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 75.00,
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

    public function test_direction_matters_so_to_sw_is_different_from_sw_to_so(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 45.00,
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

    public function test_matches_case_insensitively(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'so',
            'to_postcode_area' => 'sw',
            'price' => 45.00,
            'is_active' => true,
        ]);

        $result = $this->calculator->calculate(
            $this->operator->id,
            $this->standardFleet->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertNotNull($result);
        $this->assertEquals(45.00, $result);
    }

    // -------------------------------------------------------
    // calculateAll() tests
    // -------------------------------------------------------

    public function test_calculate_all_returns_all_fleet_type_prices(): void
    {
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 45.00,
            'is_active' => true,
        ]);

        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 65.00,
            'is_active' => true,
        ]);

        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertCount(2, $results);
        $this->assertEquals(45.00, $results[$this->standardFleet->id]);
        $this->assertEquals(65.00, $results[$this->mpvFleet->id]);
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
        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->standardFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 45.00,
            'is_active' => true,
        ]);

        PostcodeAreaPrice::create([
            'operator_id' => $this->operator->id,
            'fleet_type_id' => $this->mpvFleet->id,
            'from_postcode_area' => 'SO',
            'to_postcode_area' => 'SW',
            'price' => 65.00,
            'is_active' => false,
        ]);

        $results = $this->calculator->calculateAll(
            $this->operator->id,
            'SO14 2AA',
            'SW1A 1AA'
        );

        $this->assertCount(1, $results);
        $this->assertEquals(45.00, $results[$this->standardFleet->id]);
        $this->assertArrayNotHasKey($this->mpvFleet->id, $results);
    }
}
