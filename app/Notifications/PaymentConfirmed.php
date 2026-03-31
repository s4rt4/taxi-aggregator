<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $paymentMethod = $this->payment->card_brand
            ? "{$this->payment->card_brand} ending in {$this->payment->card_last4}"
            : ($this->payment->payment_method ?? 'Card');

        return (new MailMessage)
            ->subject("Payment Confirmed - {$this->booking->reference}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Payment has been successfully processed for your booking.")
            ->line("**Booking Reference:** {$this->booking->reference}")
            ->line("**Amount:** £{$this->payment->amount}")
            ->line("**Payment Method:** {$paymentMethod}")
            ->line('Thank you for your payment.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmed',
            'booking_id' => $this->booking->id,
            'payment_id' => $this->payment->id,
            'reference' => $this->booking->reference,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'card_brand' => $this->payment->card_brand,
            'card_last4' => $this->payment->card_last4,
            'message' => "Payment of £{$this->payment->amount} confirmed for booking {$this->booking->reference}.",
        ];
    }
}
