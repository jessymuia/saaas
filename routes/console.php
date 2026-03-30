<?php

use App\Jobs\CalculateUsageMetrics;
use App\Jobs\CheckSubscriptionStatus;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CheckSubscriptionStatus)
    ->dailyAt('00:00')
    ->name('check-subscription-status')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('CheckSubscriptionStatus job failed.');
    });

Schedule::job(new CalculateUsageMetrics)
    ->dailyAt('01:00')
    ->name('calculate-usage-metrics')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('CalculateUsageMetrics job failed.');
    });