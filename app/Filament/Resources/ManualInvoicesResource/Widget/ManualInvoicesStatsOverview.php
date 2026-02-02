<?php

namespace App\Filament\Resources\ManualInvoicesResource\Widget;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ManualInvoices;
use Illuminate\Support\Facades\Auth;

class ManualInvoicesStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Base query, respecting user access
        $query = ManualInvoices::accessibleByUser(Auth::user());

        // Total Invoices
        $totalInvoices = $query->count();

        // Total Invoiced Amount (sum of appended 'amount')
        $totalInvoiced = $query->get()->sum('amount'); // Uses appended attribute

        // Total Unpaid Amount (sum of appended 'unpaid_amount')
        $totalUnpaid = $query->get()->sum('unpaid_amount'); // Uses appended attribute

        // Confirmed Invoices
        $confirmedInvoices = $query->clone()->where('is_confirmed', true)->count();

        // Generated Documents
        $generatedDocuments = $query->clone()->where('is_generated', true)->count();

        return [
            Stat::make('Total Manual Invoices', $totalInvoices)
                ->description('All invoices created')
                ->icon('heroicon-o-document-text'),
            Stat::make('Total Invoiced Amount', 'KES ' . number_format($totalInvoiced, 2))
                ->description('Sum of all invoice amounts')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Total Unpaid Amount', 'KES ' . number_format($totalUnpaid, 2))
                ->description('Outstanding dues after payments/credits')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($totalUnpaid > 0 ? 'danger' : 'success'),
            Stat::make('Confirmed Invoices', $confirmedInvoices)
                ->description('Invoices ready for action')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

        ];
    }
}
