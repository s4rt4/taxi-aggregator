<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingReceived extends Notification implements ShouldQueue
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
        $fleetTypeName = $this->booking->fleetType?->name ?? 'N/A';

        return (new MailMessage)
            ->subject("New Booking - {$this->booking->reference}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have received a new booking!")
            ->line("**Booking Reference:** {$this->booking->reference}")
            ->line("**Pickup:** {$this->booking->pickup_address}")
            ->line("**Destination:** {$this->booking->destination_address}")
            ->line("**Date/Time:** {$this->booking->pickup_datetime->format('D, d M Y \\a\\t H:i')}")
            ->line("**Passenger:** {$this->booking->passenger_name}")
            ->line("**Fleet Type:** {$fleetTypeName}")
            ->line("**Total Price:** £{$this->booking->total_price}")
            ->action('View in Dashboard', route('operator.bookings.index'))
            ->line('Please review and accept this booking promptly.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_booking_received',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'pickup_address' => $this->booking->pickup_address,
            'destination_address' => $this->booking->destination_address,
            'pickup_datetime' => $this->booking->pickup_datetime->toIso8601String(),
            'passenger_name' => $this->booking->passenger_name,
            'fleet_type' => $this->booking->fleetType?->name,
            'total_price' => $this->booking->total_price,
            'message' => "New booking {$this->booking->reference} from {$this->booking->passenger_name}.",
        ];
    }
}
