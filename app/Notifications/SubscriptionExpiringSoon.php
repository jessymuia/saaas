<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = $this->subscription->daysUntilTrialEnds();

        return (new MailMessage)
            ->subject('Your PropManage trial expires in ' . $days . ' days')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your 60-day free trial is ending in ' . $days . ' days.')
            ->line('After your trial ends, you will have a 3-day grace period before your account is suspended.')
            ->line('Subscribe now to keep access to all your properties, units, and tenant data.')
            ->action('Subscribe Now', url('/app/login'))
            ->line('If you have any questions, please contact our support team.')
            ->salutation('The PropManage Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'trial_expiring',
            'message'       => 'Your trial expires in ' . $this->subscription->daysUntilTrialEnds() . ' days.',
            'trial_ends_at' => $this->subscription->trial_ends_at,
            'plan'          => $this->subscription->plan->name ?? 'Trial',
        ];
    }
}