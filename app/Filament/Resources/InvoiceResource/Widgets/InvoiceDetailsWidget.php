<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetailsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var Invoice|null $invoice */
        $invoice = $this->record;

        if (!$invoice) {
            return [];
        }

        // Use the model's accessors and methods
        $totalAmount = $invoice->amount;  // getAmountAttribute
        $paidAmount = $invoice->invoicePayments()->sum('amount') ?? 0;
        $creditedAmount = $invoice->creditNote()->sum('amount_credited') ?? 0;
        $outstanding = $invoice->unpaid_amount ?? ($totalAmount - $paidAmount - $creditedAmount);

        return [
            Stat::make('Invoice Total', 'KSh ' . number_format($totalAmount, 2))
                ->description('Including VAT')
                ->descriptionIcon('heroicon-m-document-currency-dollar')
                ->color('blue'),

            Stat::make('Amount Paid', 'KSh ' . number_format($paidAmount, 2))
                ->description('Received payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Credited Amount', 'KSh ' . number_format($creditedAmount, 2))
                ->description('From credit notes')
                ->descriptionIcon('heroicon-m-receipt-refund')
                ->color('info'),

            Stat::make('Balance Due', 'KSh ' . number_format($outstanding, 2))
                ->description('Outstanding')
                ->descriptionIcon('heroicon-m-scale')
                ->color($outstanding > 0 ? 'danger' : 'success'),
        ];
    }
}