<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringSoon;
use App\Notifications\SubscriptionGracePeriodEnding;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $this->handleExpiringTrials();
        $this->handleExpiredTrials();
        $this->handleExpiringGracePeriods();
        $this->handleExpiredGracePeriods();
    }

    // Send reminder 7 days before trial ends
    private function handleExpiringTrials(): void
    {
        Subscription::where('status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->where(function ($q) {
                $q->whereNull('last_reminded_at')
                  ->orWhere('last_reminded_at', '<', now()->subDays(1));
            })
            ->with('client')
            ->each(function (Subscription $subscription) {
                try {
                    $subscription->client->notify(
                        new SubscriptionExpiringSoon($subscription)
                    );
                    $subscription->update([
                        'last_reminded_at' => now(),
                        'reminder_count'   => $subscription->reminder_count + 1,
                    ]);
                    Log::info("Trial expiry reminder sent to {$subscription->saas_client_id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send trial reminder: {$e->getMessage()}");
                }
            });
    }

    // Move expired trials to grace period
    private function handleExpiredTrials(): void
    {
        Subscription::where('status', 'trialing')
            ->where('trial_ends_at', '<', now())
            ->with('client')
            ->each(function (Subscription $subscription) {
                $subscription->startGracePeriod();
                Log::info("Trial expired, grace period started for {$subscription->saas_client_id}");
            });
    }

    // Send reminder 1 day before grace period ends
    private function handleExpiringGracePeriods(): void
    {
        Subscription::where('status', 'grace_period')
            ->whereNotNull('grace_ends_at')
            ->whereBetween('grace_ends_at', [now(), now()->addDays(1)])
            ->where(function ($q) {
                $q->whereNull('last_reminded_at')
                  ->orWhere('last_reminded_at', '<', now()->subHours(12));
            })
            ->with('client')
            ->each(function (Subscription $subscription) {
                try {
                    $subscription->client->notify(
                        new SubscriptionGracePeriodEnding($subscription)
                    );
                    $subscription->update([
                        'last_reminded_at' => now(),
                        'reminder_count'   => $subscription->reminder_count + 1,
                    ]);
                    Log::info("Grace period ending reminder sent to {$subscription->saas_client_id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send grace reminder: {$e->getMessage()}");
                }
            });
    }

    // Suspend tenants whose grace period has ended
    private function handleExpiredGracePeriods(): void
    {
        Subscription::where('status', 'grace_period')
            ->where('grace_ends_at', '<', now())
            ->with('client')
            ->each(function (Subscription $subscription) {
                $subscription->suspend();
                Log::info("Tenant suspended: {$subscription->saas_client_id}");
            });
    }
}