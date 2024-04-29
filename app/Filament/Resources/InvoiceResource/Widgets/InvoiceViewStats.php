<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;

class InvoiceViewStats extends BaseWidget
{
    protected function getStats(): array
    {
        // read the current url and get the invoice id
        $url = url()->current();
        $urlParts = explode('/', $url);
        $invoiceID = end($urlParts);

        return [
            //
            Stat::make('Total Invoices', 'count', 'invoices')
                ->icon('heroicon-o-document-text')
                ->color('bg-blue-500', 'text-white'),

        ];
    }
}
