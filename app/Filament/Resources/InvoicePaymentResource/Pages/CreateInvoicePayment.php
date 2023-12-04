<?php

namespace App\Filament\Resources\InvoicePaymentResource\Pages;

use App\Filament\Resources\InvoicePaymentResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoicePayment extends CreateRecord
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Invoice::find($data['invoice_id'])->tenancyAgreement->tenant_id;
        $data['created_by'] = auth()->user()->id;
        $data['received_by'] = auth()->user()->id;

        return $data;
    }
}
