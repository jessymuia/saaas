<?php

namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process subscription renewals daily
 * Run via cron: 0 0 * * * php artisan schedule:run
 */
class ProcessSubscriptionRenewals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info('Starting subscription renewal batch process...');

        $results = BillingService::processRenewals();

        Log::info('Subscription renewal batch completed', $results);

        if ($results['failed'] > 0) {
            Log::warning(
                "Renewal batch completed with {$results['failed']} failures. " .
                "Details: " . json_encode($results)
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(
            'ProcessSubscriptionRenewals job failed: ' . $exception->getMessage(),
            ['exception' => $exception]
        );
    }
}