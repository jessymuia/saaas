<?php

namespace App\Filament\Resources\App\InvoiceResource\Pages;

use App\Filament\Resources\App\InvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;
}
