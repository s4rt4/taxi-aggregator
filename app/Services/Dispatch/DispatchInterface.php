<?php

namespace App\Services\Dispatch;

use App\Models\Booking;

interface DispatchInterface
{
    public function createJob(Booking $booking): array;

    public function cancelJob(Booking $booking): bool;

    public function getJobStatus(Booking $booking): ?string;
}
