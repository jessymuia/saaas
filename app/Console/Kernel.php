<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // TODO: Check on the necessity of this command
        $schedule->command('app:generate-monthly-rent-bills-command')->monthlyOn(27, '00:00')->withoutOverlapping();
        $schedule->command('app:escalate-amounts-command')->dailyAt('00:00')->withoutOverlapping();
        $schedule->job(new \App\Jobs\ProcessSubscriptionRenewals())
        ->dailyAt('00:00')
        ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
