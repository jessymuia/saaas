<?php

namespace App\Filament\Resources\InvoicePaymentResource\Pages;

use App\Filament\Resources\InvoicePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoicePayments extends ListRecords
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
