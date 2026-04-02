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

    public string $queue = 'emails';

    public int $tries = 3;

    public int $backoff = 60;

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

            $this->fail($e);
        }
    }
}
