<?php

namespace App\Listeners;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\NewBookingReceived;

class SendBookingNotifications
{
    /**
     * Notify the passenger that their booking has been confirmed
     * and the operator that a new booking has been received.
     */
    public static function onBookingCreated(Booking $booking): void
    {
        $booking->loadMissing(['operator.user', 'fleetType', 'passenger']);

        // Notify passenger
        $passenger = $booking->passenger;
        if ($passenger) {
            $passenger->notify(new BookingConfirmed($booking));
        }

        // Notify operator
        $operatorUser = $booking->operator?->user;
        if ($operatorUser) {
            $operatorUser->notify(new NewBookingReceived($booking));
        }
    }

    /**
     * Notify the passenger when the booking status changes.
     */
    public static function onBookingStatusUpdated(Booking $booking, string $newStatus): void
    {
        $booking->loadMissing(['operator', 'passenger']);

        $passenger = $booking->passenger;
        if ($passenger) {
            $passenger->notify(new BookingStatusUpdated($booking, $newStatus));
        }
    }

    /**
     * Notify the other party when a booking is cancelled.
     *
     * If cancelled by passenger -> notify operator.
     * If cancelled by operator -> notify passenger.
     */
    public static function onBookingCancelled(Booking $booking, string $cancelledBy, ?string $reason = null): void
    {
        $booking->loadMissing(['operator.user', 'passenger']);

        if ($cancelledBy === 'passenger') {
            // Notify operator
            $operatorUser = $booking->operator?->user;
            if ($operatorUser) {
                $operatorUser->notify(new BookingCancelled($booking, $cancelledBy, $reason));
            }
        } else {
            // Notify passenger
            $passenger = $booking->passenger;
            if ($passenger) {
                $passenger->notify(new BookingCancelled($booking, $cancelledBy, $reason));
            }
        }
    }
}
