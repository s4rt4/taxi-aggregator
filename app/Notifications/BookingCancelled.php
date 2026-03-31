<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $cancelledBy,
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cancelledByLabel = ucfirst($this->cancelledBy);

        $mail = (new MailMessage)
            ->subject("Booking Cancelled - {$this->booking->reference}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A booking has been cancelled.")
            ->line("**Booking Reference:** {$this->booking->reference}")
            ->line("**Cancelled By:** {$cancelledByLabel}")
            ->line("**Pickup:** {$this->booking->pickup_address}")
            ->line("**Destination:** {$this->booking->destination_address}")
            ->line("**Date/Time:** {$this->booking->pickup_datetime->format('D, d M Y \\a\\t H:i')}");

        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }

        $mail->line('If you have any questions, please contact our support team.');

        return $mail;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_cancelled',
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'cancelled_by' => $this->cancelledBy,
            'reason' => $this->reason,
            'pickup_address' => $this->booking->pickup_address,
            'destination_address' => $this->booking->destination_address,
            'pickup_datetime' => $this->booking->pickup_datetime->toIso8601String(),
            'message' => "Booking {$this->booking->reference} has been cancelled by {$this->cancelledBy}.",
        ];
    }
}
