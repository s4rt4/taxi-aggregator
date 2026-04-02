<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use App\Events\NewBookingEvent;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\NewBookingReceived;
use App\Helpers\Settings;
use App\Services\SmsService;

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

        // SMS to passenger
        $companyName = Settings::get('company_name', config('app.name'));
        if ($booking->passenger_phone) {
            SmsService::send($booking->passenger_phone,
                "Booked via {$companyName}. Ref: {$booking->reference}. {$booking->pickup_address} to {$booking->destination_address} on " .
                \Carbon\Carbon::parse($booking->pickup_datetime)->format('d/m/Y H:i') . ". Total: GBP {$booking->total_price}");
        }

        // Broadcast real-time event to operator
        event(new NewBookingEvent($booking));
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

        // SMS to passenger
        if ($booking->passenger_phone) {
            SmsService::send($booking->passenger_phone,
                "Booking {$booking->reference} update: Status is now {$newStatus}.");
        }

        // Broadcast real-time event to passenger
        event(new BookingStatusChanged($booking, $newStatus));
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

            // SMS to operator's phone
            $operatorPhone = $booking->operator?->phone;
            if ($operatorPhone) {
                SmsService::send($operatorPhone,
                    "Booking {$booking->reference} has been cancelled by the passenger." .
                    ($reason ? " Reason: {$reason}" : ''));
            }
        } else {
            // Notify passenger
            $passenger = $booking->passenger;
            if ($passenger) {
                $passenger->notify(new BookingCancelled($booking, $cancelledBy, $reason));
            }

            // SMS to passenger's phone
            if ($booking->passenger_phone) {
                SmsService::send($booking->passenger_phone,
                    "Booking {$booking->reference} has been cancelled by the operator." .
                    ($reason ? " Reason: {$reason}" : ''));
            }
        }
    }
}
