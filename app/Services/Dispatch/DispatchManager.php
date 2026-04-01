<?php

namespace App\Services\Dispatch;

use App\Models\Booking;
use App\Models\Operator;

class DispatchManager
{
    public static function for(Operator $operator): DispatchInterface
    {
        if ($operator->usesIcabbi()) {
            return new IcabbiDispatch($operator);
        }

        return new ManualDispatch();
    }

    public static function dispatchBooking(Booking $booking): array
    {
        $operator = $booking->operator;
        if (!$operator) {
            return ['dispatched' => false, 'error' => 'No operator'];
        }

        $handler = self::for($operator);
        return $handler->createJob($booking);
    }
}
