<?php

namespace App\Filament\Resources\SentEmailsResource\Pages\Widgets;

use App\Models\SentEmails;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SentEmailsStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = SentEmails::count();

        $sent    = SentEmails::where('delivery_status', 'SENT')->count();
        $failed  = SentEmails::where('delivery_status', 'FAILED')->count();
        $pending = SentEmails::where('delivery_status', 'PENDING')->count();

        $sentToday = SentEmails::where('delivery_status', 'SENT')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $successRate = $total > 0 ? round(($sent / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Emails', number_format($total))
                ->description('All email records')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('gray'),

            Stat::make('Successfully Sent', number_format($sent))
                ->description("{$successRate}% success rate")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Failed', number_format($failed))
                ->description($failed === 1 ? '1 delivery issue' : "{$failed} delivery issues")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Pending', number_format($pending))
                ->description($pending === 1 ? '1 in queue' : "{$pending} in queue")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Sent Today', number_format($sentToday))
                ->description('Successful deliveries today')
                ->color('success'),
        ];
    }
}
