<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Review $review
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stars = str_repeat('*', $this->review->rating);
        $commentPreview = $this->review->comment
            ? \Illuminate\Support\Str::limit($this->review->comment, 100)
            : 'No comment provided.';

        return (new MailMessage)
            ->subject("New Review - {$this->review->rating}/5 stars")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have received a new review from a passenger.")
            ->line("**Rating:** {$this->review->rating}/5 stars")
            ->line("**Comment:** {$commentPreview}")
            ->line("**Booking Reference:** {$this->review->booking->reference}")
            ->action('View Reviews', route('operator.issues.index'))
            ->line('Thank you for providing great service!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_review_received',
            'review_id' => $this->review->id,
            'booking_id' => $this->review->booking_id,
            'reference' => $this->review->booking->reference,
            'rating' => $this->review->rating,
            'comment_preview' => $this->review->comment
                ? \Illuminate\Support\Str::limit($this->review->comment, 100)
                : null,
            'message' => "New {$this->review->rating}/5 star review for booking {$this->review->booking->reference}.",
        ];
    }
}
