<?php

namespace App\Notifications;

use App\Models\Operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperatorRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Operator $operator,
        protected string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Operator Application Update')
            ->greeting("Hello {$notifiable->name},")
            ->line("We have reviewed your operator application for **{$this->operator->operator_name}** and unfortunately we are unable to approve it at this time.")
            ->line("**Reason:** {$this->reason}")
            ->line('If you believe this decision was made in error, or if you have addressed the issues mentioned above, you are welcome to reapply by updating your operator details and contacting our support team.')
            ->line('Thank you for your interest in joining our platform.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'operator_rejected',
            'operator_id' => $this->operator->id,
            'operator_name' => $this->operator->operator_name,
            'reason' => $this->reason,
            'message' => "Your operator application for '{$this->operator->operator_name}' was not approved.",
        ];
    }
}
