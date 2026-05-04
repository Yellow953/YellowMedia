<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SyncErrorNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $accountName,
        private readonly string $error,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Meta Ads Sync Failed — {$this->accountName}")
            ->greeting('Sync Error')
            ->line("We encountered an error syncing your Meta Ads account: **{$this->accountName}**")
            ->line("**Error:** {$this->error}")
            ->action('Manage Ad Accounts', route('settings.ad-accounts'))
            ->line('If this keeps happening, try reconnecting your account.');
    }
}
