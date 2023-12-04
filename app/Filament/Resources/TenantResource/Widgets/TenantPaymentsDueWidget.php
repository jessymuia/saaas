<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Invoice;
use App\Models\TenancyAgreement;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantPaymentsDueWidget extends BaseWidget
{
    protected $tenantID;
    public function __construct()
    {
        $url = session()->get('_previous')['url'];
        // get the last part of the url
        $url = explode('/',$url);
        $this->tenantID = end($url);
    }

    protected function getStats(): array
    {


        return [
            //
            Stat::make(
                'Tenancy Agreements',
                TenancyAgreement::query()->where('id','=',1)->count())
                ->color('primary')
                ->icon('heroicon-m-clipboard-document-check')
                ->description('Total number of tenancy agreements')
                ->descriptionIcon('heroicon-m-clipboard-document-list'),
            Stat::make(
                'Payments Due',
                $this->tenantBillsBilled() - $this->tenantPaymentsProcessed())
                ->color('primary')
                ->description('Total amount of payments due')
                ->descriptionIcon('heroicon-m-banknotes'),
            Stat::make(
                'Payments Processed',
                $this->tenantPaymentsProcessed())
                ->color('primary')
                ->description('Total amount of payments processed')
                ->descriptionIcon('heroicon-m-currency-dollar'),
        ];
    }

    protected function tenantPaymentsProcessed(){
        // get all payments belonging to this given tenant tenancy agreements
        $tenancyAgreements = TenancyAgreement::query()
            ->where('tenant_id','=',$this->tenantID)
            ->with('invoicePayments')
            ->get();

        $invoicePayments = array_merge(...$tenancyAgreements->pluck('invoicePayments')->toArray());

        $totalPayments = 0;

        foreach($invoicePayments as $payment){
            $totalPayments += $payment['amount'];
        }

        return $totalPayments;
    }

    protected function tenantBillsBilled(){
        $totalTenantInvoicesSum = 0;
        // display session data


        $tenants = Tenant::query()
            ->where('id','=',$this->tenantID)
            ->with('invoices')
            ->get();

        $tenantInvoices = array_merge(...$tenants->pluck('invoices')->toArray());

        foreach($tenantInvoices as $invoice)
        {
            $totalTenantInvoicesSum += $invoice['amount'];
        }

        return $totalTenantInvoicesSum;
    }
}
