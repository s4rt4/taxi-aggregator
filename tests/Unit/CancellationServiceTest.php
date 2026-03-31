<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Services\CancellationService;
use Carbon\Carbon;
use Tests\TestCase;

class CancellationServiceTest extends TestCase
{
    private function makeBooking(string $pickupDatetime, float $totalPrice = 100.00): Booking
    {
        $booking = new Booking();
        $booking->pickup_datetime = Carbon::parse($pickupDatetime);
        $booking->total_price = $totalPrice;
        return $booking;
    }

    public function test_72_hours_before_returns_full_refund(): void
    {
        // 72 hours from now -> 100% refund
        $booking = $this->makeBooking(now()->addHours(72)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(100, $result['refund_percent']);
        $this->assertEquals(100.00, $result['refund_amount']);
        $this->assertEquals('Full refund', $result['policy']);
    }

    public function test_36_hours_before_returns_75_percent_refund(): void
    {
        // 36 hours from now -> 75% refund
        $booking = $this->makeBooking(now()->addHours(36)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(75, $result['refund_percent']);
        $this->assertEquals(75.00, $result['refund_amount']);
        $this->assertEquals('75% refund', $result['policy']);
    }

    public function test_12_hours_before_returns_50_percent_refund(): void
    {
        // 12 hours from now -> 50% refund
        $booking = $this->makeBooking(now()->addHours(12)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(50, $result['refund_percent']);
        $this->assertEquals(50.00, $result['refund_amount']);
        $this->assertEquals('50% refund', $result['policy']);
    }

    public function test_3_hours_before_returns_25_percent_refund(): void
    {
        // 3 hours from now -> 25% refund
        $booking = $this->makeBooking(now()->addHours(3)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(25, $result['refund_percent']);
        $this->assertEquals(25.00, $result['refund_amount']);
        $this->assertEquals('25% refund', $result['policy']);
    }

    public function test_1_hour_before_returns_no_refund(): void
    {
        // 1 hour from now -> 0% refund
        $booking = $this->makeBooking(now()->addHours(1)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(0, $result['refund_percent']);
        $this->assertEquals(0, $result['refund_amount']);
        $this->assertEquals('No refund', $result['policy']);
    }

    public function test_past_pickup_returns_no_refund(): void
    {
        // 2 hours ago -> no refund
        $booking = $this->makeBooking(now()->subHours(2)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(0, $result['refund_percent']);
        $this->assertEquals(0, $result['refund_amount']);
        $this->assertStringContains('passed', $result['policy']);
    }

    public function test_refund_amount_scales_with_price(): void
    {
        // 72 hours, £200 booking -> £200 refund
        $booking = $this->makeBooking(now()->addHours(72)->toDateTimeString(), 200.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(200.00, $result['refund_amount']);
    }

    public function test_refund_amount_with_decimal_price(): void
    {
        // 36 hours, £79.99 booking -> 75% = £59.99
        $booking = $this->makeBooking(now()->addHours(36)->toDateTimeString(), 79.99);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(59.99, $result['refund_amount']);
    }

    public function test_result_contains_hours_until_pickup(): void
    {
        $booking = $this->makeBooking(now()->addHours(50)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertArrayHasKey('hours_until_pickup', $result);
    }

    public function test_get_policy_text_returns_array(): void
    {
        $text = CancellationService::getPolicyText();

        $this->assertIsArray($text);
        $this->assertCount(5, $text);
    }

    public function test_exactly_48_hours_returns_full_refund(): void
    {
        $booking = $this->makeBooking(now()->addHours(49)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(100, $result['refund_percent']);
    }

    public function test_exactly_24_hours_returns_75_percent(): void
    {
        $booking = $this->makeBooking(now()->addHours(25)->toDateTimeString(), 100.00);

        $result = CancellationService::getRefundAmount($booking);

        $this->assertEquals(75, $result['refund_percent']);
    }

    /**
     * Helper to assert a string contains a substring (for PHP compatibility).
     */
    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'."
        );
    }
}
