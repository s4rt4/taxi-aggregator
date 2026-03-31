<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = str_replace('_', ' ', ucfirst($this->newStatus));

        return (new MailMessage)
            ->subject("Booking Update - {$this->booking->reference} is now {$statusLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your booking status has been updated.")
            ->line("**Booking Reference:** {$this->booking->reference}")
            ->line("**New Status:** {$statusLabel}")
            ->line("**Operator:** {$this->booking->operator->operator_name}")
            ->line("**Pickup:** {$this->booking->pickup_address}")
            ->line("**Date/Time:** {$this->booking->pickup_datetime->format('D, d M Y \\a\\t H:i')}")
            ->action('View Booking', route('passenger.booking-detail', $this->booking))
            ->line('Thank you for using our service.');
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabel = str_replace('_', ' ', ucfirst($this->newStatus));

        return [
            'type' => 'booking_status_updated',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'new_status' => $this->newStatus,
            'status_label' => $statusLabel,
            'operator_name' => $this->booking->operator->operator_name,
            'pickup_address' => $this->booking->pickup_address,
            'message' => "Booking {$this->booking->reference} is now {$statusLabel}.",
        ];
    }
}
