<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Operator;
use App\Services\Dispatch\DispatchManager;
use App\Services\Dispatch\IcabbiDispatch;
use App\Services\Dispatch\ManualDispatch;
use Tests\TestCase;

class DispatchManagerTest extends TestCase
{
    public function test_manual_dispatch_returns_dispatched_false(): void
    {
        $booking = new Booking(['status' => 'pending']);
        $dispatch = new ManualDispatch();

        $result = $dispatch->createJob($booking);

        $this->assertFalse($result['dispatched']);
        $this->assertEquals('manual', $result['method']);
    }

    public function test_manual_dispatch_cancel_returns_true(): void
    {
        $booking = new Booking(['status' => 'pending']);
        $dispatch = new ManualDispatch();

        $this->assertTrue($dispatch->cancelJob($booking));
    }

    public function test_manual_dispatch_get_status_returns_booking_status(): void
    {
        $booking = new Booking(['status' => 'accepted']);
        $dispatch = new ManualDispatch();

        $this->assertEquals('accepted', $dispatch->getJobStatus($booking));
    }

    public function test_dispatch_manager_returns_manual_dispatch_for_operator_without_icabbi(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => false,
            'icabbi_app_key' => null,
            'icabbi_secret_key' => null,
        ]);

        $handler = DispatchManager::for($operator);

        $this->assertInstanceOf(ManualDispatch::class, $handler);
    }

    public function test_dispatch_manager_returns_icabbi_dispatch_for_operator_with_icabbi_enabled(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => true,
            'icabbi_app_key' => 'test-app-key',
            'icabbi_secret_key' => 'test-secret-key',
            'icabbi_api_url' => 'https://api.icabbidispatch.com/icd/',
        ]);

        $handler = DispatchManager::for($operator);

        $this->assertInstanceOf(IcabbiDispatch::class, $handler);
    }

    public function test_dispatch_manager_returns_manual_dispatch_when_icabbi_enabled_but_keys_missing(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => true,
            'icabbi_app_key' => null,
            'icabbi_secret_key' => null,
        ]);

        $handler = DispatchManager::for($operator);

        $this->assertInstanceOf(ManualDispatch::class, $handler);
    }

    public function test_operator_uses_icabbi_returns_true_when_fully_configured(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => true,
            'icabbi_app_key' => 'test-app-key',
            'icabbi_secret_key' => 'test-secret-key',
        ]);

        $this->assertTrue($operator->usesIcabbi());
    }

    public function test_operator_uses_icabbi_returns_false_when_disabled(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => false,
            'icabbi_app_key' => 'test-app-key',
            'icabbi_secret_key' => 'test-secret-key',
        ]);

        $this->assertFalse($operator->usesIcabbi());
    }

    public function test_operator_uses_icabbi_returns_false_when_app_key_missing(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => true,
            'icabbi_app_key' => null,
            'icabbi_secret_key' => 'test-secret-key',
        ]);

        $this->assertFalse($operator->usesIcabbi());
    }

    public function test_operator_uses_icabbi_returns_false_when_secret_key_missing(): void
    {
        $operator = new Operator([
            'icabbi_enabled' => true,
            'icabbi_app_key' => 'test-app-key',
            'icabbi_secret_key' => null,
        ]);

        $this->assertFalse($operator->usesIcabbi());
    }
}
