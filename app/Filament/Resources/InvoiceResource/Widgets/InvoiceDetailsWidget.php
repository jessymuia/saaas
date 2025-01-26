<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceDetailsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // get the current invoice id from the referred url
        $url = request()->headers->get('referer');
        $urlParts = explode('/', $url);
        $invoiceID = end($urlParts);

        // get the invoice amount
//         $invoice = \App\Models\Invoice::find($invoiceID);

        return [
//            Stat::make('Total Due', $invoice->totalDue())
//                ->icon('heroicon-o-currency-dollar')
//                ->color('bg-blue-500', 'text-white'),
//            Stat::make('Invoice Amount', $invoice->amount)
//                ->icon('heroicon-o-currency-dollar')
//                ->color('bg-blue-500', 'text-white'),
//            Stat::make('Total Paid', $invoice->invoicePayments()->sum('amount'))
//                ->icon('heroicon-o-currency-dollar')
//                ->color('bg-blue-500', 'text-white'),
        ];
    }
}
