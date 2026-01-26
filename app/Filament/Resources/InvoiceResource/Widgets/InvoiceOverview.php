<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InvoiceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Invoice::accessibleByUser(Auth::user())
            ->with(['tenancyBills', 'invoicePayments', 'creditNote']);
        
        $totalInvoices = $query->count();
        $confirmedInvoices = $query->clone()->where('is_confirmed', true)->count();
        $generatedInvoices = $query->clone()->where('is_generated', true)->count();
        
        // Get all invoices
        $invoices = $query->get();
        
        // Calculate totals
        $totalAmount = $invoices->sum('amount');
        $totalUnpaid = $invoices->sum('unpaid_amount');
        
        return [
            Stat::make('Total Invoices', $totalInvoices)
                ->description('All invoices')
                ->color('primary')
                ->icon('heroicon-o-document-text'),
            
            Stat::make('Confirmed Invoices', $confirmedInvoices)
                ->description('Confirmed by admin')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            
            Stat::make('Generated Documents', $generatedInvoices)
                ->description('PDFs generated')
                ->color('warning')
                ->icon('heroicon-o-document'),
            
            Stat::make('Total Amount', 'KES ' . number_format($totalAmount, 2))
                ->description('Sum of all invoices')
                ->color('info')
                ->icon('heroicon-o-currency-dollar'),
            
            Stat::make('Total Unpaid', 'KES ' . number_format($totalUnpaid, 2))
                ->description('Outstanding amount')
                ->color($totalUnpaid > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}