<?php

namespace App\Filament\Resources\App\InvoicePaymentResource\Pages;

use App\Filament\Resources\App\InvoicePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoicePayment extends CreateRecord
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['received_by']   = auth()->id();
        $data['saas_client_id'] = filament()->getTenant()?->id;

        return $data;
    }
}
