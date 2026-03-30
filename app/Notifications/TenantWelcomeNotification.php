<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $tenantName,
        public string $loginUrl,
        public string $email,
        public string $password,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Welcome to PropManage — Your Account is Ready")
            ->greeting("Hello {$this->tenantName}!")
            ->line("Your PropManage SaaS account has been created successfully.")
            ->line("**Login URL:** {$this->loginUrl}")
            ->line("**Email:** {$this->email}")
            ->line("**Password:** {$this->password}")
            ->action('Login Now', $this->loginUrl)
            ->line("Please change your password after your first login.")
            ->salutation('The PropManage Team');
    }
}