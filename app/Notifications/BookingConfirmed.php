<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Booking Confirmed - {$this->booking->reference}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your booking has been confirmed! Here are the details:")
            ->line("**Booking Reference:** {$this->booking->reference}")
            ->line("**Pickup:** {$this->booking->pickup_address}")
            ->line("**Destination:** {$this->booking->destination_address}")
            ->line("**Date/Time:** {$this->booking->pickup_datetime->format('D, d M Y \\a\\t H:i')}")
            ->line("**Operator:** {$this->booking->operator->operator_name}")
            ->line("**Total Price:** £{$this->booking->total_price}")
            ->action('View Booking', route('passenger.booking-detail', $this->booking))
            ->line('Thank you for choosing ' . \App\Helpers\Settings::get('company_name', config('app.name')) . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'pickup_address' => $this->booking->pickup_address,
            'destination_address' => $this->booking->destination_address,
            'pickup_datetime' => $this->booking->pickup_datetime->toIso8601String(),
            'operator_name' => $this->booking->operator->operator_name,
            'total_price' => $this->booking->total_price,
            'message' => "Booking {$this->booking->reference} has been confirmed.",
        ];
    }
}
