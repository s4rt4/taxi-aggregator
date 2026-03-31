<?php

namespace Tests\Unit;

use App\Services\VatService;
use PHPUnit\Framework\TestCase;

class VatServiceTest extends TestCase
{
    public function test_calculate_vat_from_net_amount(): void
    {
        // £100 net -> £20 VAT
        $this->assertEquals(20.00, VatService::calculateVat(100.00));
    }

    public function test_calculate_vat_with_decimal_amount(): void
    {
        // £49.99 net -> £10.00 VAT (rounded)
        $this->assertEquals(10.00, VatService::calculateVat(49.99));
    }

    public function test_calculate_vat_with_zero(): void
    {
        $this->assertEquals(0.00, VatService::calculateVat(0));
    }

    public function test_add_vat_to_net_amount(): void
    {
        // £100 net + 20% VAT = £120
        $this->assertEquals(120.00, VatService::addVat(100.00));
    }

    public function test_add_vat_with_decimal_amount(): void
    {
        // £49.99 + 20% = £59.99
        $this->assertEquals(59.99, VatService::addVat(49.99));
    }

    public function test_extract_vat_from_gross_amount(): void
    {
        // £120 gross -> £20 VAT
        $this->assertEquals(20.00, VatService::extractVat(120.00));
    }

    public function test_extract_vat_with_decimal_amount(): void
    {
        // £59.99 gross -> VAT component
        $vat = VatService::extractVat(59.99);
        $this->assertEquals(10.00, $vat);
    }

    public function test_net_from_gross(): void
    {
        // £120 gross -> £100 net
        $this->assertEquals(100.00, VatService::netFromGross(120.00));
    }

    public function test_net_from_gross_with_decimal(): void
    {
        // £59.99 gross -> £49.99 net
        $this->assertEquals(49.99, VatService::netFromGross(59.99));
    }

    public function test_breakdown_returns_correct_structure(): void
    {
        $result = VatService::breakdown(120.00);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('gross', $result);
        $this->assertArrayHasKey('net', $result);
        $this->assertArrayHasKey('vat', $result);
        $this->assertArrayHasKey('vat_rate', $result);
    }

    public function test_breakdown_returns_correct_values(): void
    {
        $result = VatService::breakdown(120.00);

        $this->assertEquals(120.00, $result['gross']);
        $this->assertEquals(100.00, $result['net']);
        $this->assertEquals(20.00, $result['vat']);
        $this->assertEquals(20.0, $result['vat_rate']);
    }

    public function test_breakdown_net_plus_vat_equals_gross(): void
    {
        $result = VatService::breakdown(75.50);

        $this->assertEquals(
            $result['gross'],
            round($result['net'] + $result['vat'], 2)
        );
    }

    public function test_vat_rate_is_twenty_percent(): void
    {
        $this->assertEquals(20.0, VatService::UK_VAT_RATE);
    }
}
