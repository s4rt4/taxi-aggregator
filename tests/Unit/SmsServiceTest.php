<?php

namespace Tests\Unit;

use App\Services\SmsService;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    public function test_format_uk_number_from_local(): void
    {
        $this->assertEquals('447123456789', SmsService::formatUkNumber('07123456789'));
    }

    public function test_format_uk_number_from_plus44(): void
    {
        $this->assertEquals('447123456789', SmsService::formatUkNumber('+447123456789'));
    }

    public function test_format_uk_number_from_44(): void
    {
        $this->assertEquals('447123456789', SmsService::formatUkNumber('447123456789'));
    }

    public function test_format_uk_number_strips_spaces(): void
    {
        $this->assertEquals('447123456789', SmsService::formatUkNumber('07123 456 789'));
    }

    public function test_format_uk_number_strips_dashes(): void
    {
        $this->assertEquals('447123456789', SmsService::formatUkNumber('07123-456-789'));
    }

    public function test_send_returns_false_when_not_configured(): void
    {
        // When Vonage key is not configured (default 'your_key' or empty),
        // the send method should return false without attempting an API call.
        config(['services.twilio.sid' => 'your_sid']);
        config(['services.twilio.token' => 'your_token']);

        $result = SmsService::send('07123456789', 'Test message');

        $this->assertFalse($result);
    }
}
