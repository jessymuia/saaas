<?php

namespace App\Filament\Widgets;

use App\Models\InvoicePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Users', \App\Models\User::count()),
            Stat::make('Properties managed', \App\Models\Property::count()),
            Stat::make('Amount collected', 'KES ' . number_format(InvoicePayment::sum('amount'), 2, '.', ',')),
        ];
    }
}
