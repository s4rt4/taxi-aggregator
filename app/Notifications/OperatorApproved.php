<?php

namespace App\Notifications;

use App\Models\Operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperatorApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Operator $operator
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your operator account has been approved!')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("Your operator account **{$this->operator->operator_name}** has been approved and is now active.")
            ->line('Here are your next steps:')
            ->line('1. Set up your fleet types and vehicle availability')
            ->line('2. Configure your pricing (per-mile, location-based, or both)')
            ->line('3. Add your drivers and their details')
            ->line('4. Start receiving bookings!')
            ->action('Go to Dashboard', route('operator.dashboard'))
            ->line('Welcome aboard! We look forward to working with you.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'operator_approved',
            'operator_id' => $this->operator->id,
            'operator_name' => $this->operator->operator_name,
            'message' => "Your operator account '{$this->operator->operator_name}' has been approved!",
        ];
    }
}
