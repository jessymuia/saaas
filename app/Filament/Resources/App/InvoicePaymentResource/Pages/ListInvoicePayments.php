<?php

namespace App\Filament\Resources\App\InvoicePaymentResource\Pages;

use App\Filament\Resources\App\InvoicePaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoicePayments extends ListRecords
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
