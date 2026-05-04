<?php

namespace App\Notifications;

use App\Models\MetaCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AiReviewResultNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly MetaCampaign $campaign,
        private readonly array $actions,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $highCount = count(array_filter($this->actions, fn($a) => ($a['priority'] ?? '') === 'high'));

        $mail = (new MailMessage)
            ->subject("AI Review Complete — {$this->campaign->name}")
            ->greeting('AI Campaign Review')
            ->line("Your campaign **{$this->campaign->name}** has been reviewed.")
            ->line("{$highCount} high-priority action(s) recommended.");

        foreach (array_slice($this->actions, 0, 3) as $action) {
            $priority = strtoupper($action['priority'] ?? 'medium');
            $mail->line("[{$priority}] {$action['reason']}");
        }

        return $mail
            ->action('View Campaign', route('campaigns.show', $this->campaign->id))
            ->line('Log in to review and apply these recommendations.');
    }
}
