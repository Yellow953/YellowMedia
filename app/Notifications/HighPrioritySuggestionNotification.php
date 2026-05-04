<?php

namespace App\Notifications;

use App\Models\MetaCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighPrioritySuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly MetaCampaign $campaign,
        private readonly array $suggestions,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("High-Priority Suggestions — {$this->campaign->name}")
            ->greeting('New AI Suggestions')
            ->line("We've generated high-priority recommendations for **{$this->campaign->name}**:");

        foreach (array_slice($this->suggestions, 0, 3) as $s) {
            $mail->line('• ' . $s['suggestion']);
        }

        return $mail
            ->action('View Suggestions', route('meta.campaign.show', $this->campaign->id))
            ->line('Review and apply these suggestions to improve campaign performance.');
    }
}
