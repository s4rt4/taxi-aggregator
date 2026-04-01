<?php

namespace App\Services\Dispatch;

use App\Models\Booking;

class ManualDispatch implements DispatchInterface
{
    /**
     * No-op for manual dispatch - operator handles everything in dashboard.
     */
    public function createJob(Booking $booking): array
    {
        return ['dispatched' => false, 'method' => 'manual'];
    }

    public function cancelJob(Booking $booking): bool
    {
        return true;
    }

    public function getJobStatus(Booking $booking): ?string
    {
        return $booking->status;
    }
}
