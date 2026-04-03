<?php

namespace Tests\Unit;

use App\Models\Operator;
use App\Services\CommissionService;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    protected function makeOperator(string $tier = 'basic', float $commissionRate = 0): Operator
    {
        $operator = new Operator();
        $operator->tier = $tier;
        $operator->commission_rate = $commissionRate;

        return $operator;
    }

    // --- Tier Rate Tests ---

    public function test_basic_tier_rate_is_15_percent(): void
    {
        $operator = $this->makeOperator('basic');
        $this->assertEquals(15.00, CommissionService::getRate($operator));
    }

    public function test_airport_approved_tier_rate_is_12_percent(): void
    {
        $operator = $this->makeOperator('airport_approved');
        $this->assertEquals(12.00, CommissionService::getRate($operator));
    }

    public function test_top_tier_rate_is_10_percent(): void
    {
        $operator = $this->makeOperator('top_tier');
        $this->assertEquals(10.00, CommissionService::getRate($operator));
    }

    public function test_unknown_tier_defaults_to_15_percent(): void
    {
        $operator = $this->makeOperator('unknown');
        $this->assertEquals(15.00, CommissionService::getRate($operator));
    }

    // --- Calculate Tests ---

    public function test_calculate_returns_correct_breakdown_for_basic_tier(): void
    {
        $operator = $this->makeOperator('basic');
        $result = CommissionService::calculate($operator, 100.00);

        $this->assertEquals(15.00, $result['rate']);
        $this->assertEquals(15.00, $result['commission']);
        $this->assertEquals(85.00, $result['operator_earnings']);
        $this->assertEquals(100.00, $result['booking_total']);
    }

    public function test_calculate_returns_correct_breakdown_for_airport_approved(): void
    {
        $operator = $this->makeOperator('airport_approved');
        $result = CommissionService::calculate($operator, 50.00);

        $this->assertEquals(12.00, $result['rate']);
        $this->assertEquals(6.00, $result['commission']);
        $this->assertEquals(44.00, $result['operator_earnings']);
        $this->assertEquals(50.00, $result['booking_total']);
    }

    public function test_calculate_handles_decimal_amounts(): void
    {
        $operator = $this->makeOperator('top_tier');
        $result = CommissionService::calculate($operator, 37.50);

        $this->assertEquals(10.00, $result['rate']);
        $this->assertEquals(3.75, $result['commission']);
        $this->assertEquals(33.75, $result['operator_earnings']);
    }

    // --- Custom Rate Tests ---

    public function test_custom_rate_overrides_tier_rate(): void
    {
        // Operator has basic tier (15%) but admin set custom rate of 8%
        $operator = $this->makeOperator('basic', 8.00);
        $this->assertEquals(8.00, CommissionService::getRate($operator));
    }

    public function test_custom_rate_used_in_calculation(): void
    {
        $operator = $this->makeOperator('basic', 8.00);
        $result = CommissionService::calculate($operator, 100.00);

        $this->assertEquals(8.00, $result['rate']);
        $this->assertEquals(8.00, $result['commission']);
        $this->assertEquals(92.00, $result['operator_earnings']);
    }

    public function test_tier_rate_used_when_commission_matches_tier(): void
    {
        // If commission_rate equals the tier rate, it should use the tier rate (not a "custom" override)
        $operator = $this->makeOperator('airport_approved', 12.00);
        $this->assertEquals(12.00, CommissionService::getRate($operator));
    }

    public function test_zero_commission_rate_uses_tier_default(): void
    {
        // commission_rate of 0 should fall back to tier default
        $operator = $this->makeOperator('basic', 0);
        $this->assertEquals(15.00, CommissionService::getRate($operator));
    }

    // --- Tier Constants ---

    public function test_tier_rates_constant_has_expected_values(): void
    {
        $this->assertEquals(15.00, CommissionService::TIER_RATES['basic']);
        $this->assertEquals(12.00, CommissionService::TIER_RATES['airport_approved']);
        $this->assertEquals(10.00, CommissionService::TIER_RATES['top_tier']);
        $this->assertCount(3, CommissionService::TIER_RATES);
    }
}
