<?php

namespace App\Listeners;

use App\Events\TenantRegistered;
use App\Mail\WelcomeTenantMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTenantWelcomeDetails implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Queue connection to use for this listener.
     * Inherits QUEUE_CONNECTION from .env (database in Replit, redis in production).
     */
    public string $queue = 'emails';

    /**
     * Number of times to retry the job before marking as failed.
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(TenantRegistered $event): void
    {
        try {
            Mail::to($event->user->email)->send(
                new WelcomeTenantMail($event->user, $event->plainPassword, $event->loginUrl)
            );

            Log::info('Welcome email sent to ' . $event->user->email);
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email to ' . $event->user->email . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Re-throw so the queue marks it as failed and retries
            throw $e;
        }
    }

    /**
     * Handle a job failure — called after all retries exhausted.
     */
    public function failed(TenantRegistered $event, \Throwable $exception): void
    {
        Log::critical('Welcome email permanently failed for ' . $event->user->email, [
            'exception' => $exception->getMessage(),
            'user_id'   => $event->user->id ?? null,
        ]);
    }
}
