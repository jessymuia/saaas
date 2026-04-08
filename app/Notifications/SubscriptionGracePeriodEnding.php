<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionGracePeriodEnding extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hours = (int) now()->diffInHours($this->subscription->grace_ends_at, false);

        return (new MailMessage)
            ->subject('⚠️ Your PropManage account will be suspended in ' . $hours . ' hours')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your grace period is ending in ' . $hours . ' hours.')
            ->line('After this, your account will be suspended and you will lose access to the system.')
            ->line('Your data will be kept safe — simply subscribe to restore full access immediately.')
            ->action('Subscribe Now', url('/app/login'))
            ->line('Need help? Contact our support team immediately.')
            ->salutation('The PropManage Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'grace_period_ending',
            'message'       => 'Your account will be suspended soon. Please subscribe now.',
            'grace_ends_at' => $this->subscription->grace_ends_at,
        ];
    }
}