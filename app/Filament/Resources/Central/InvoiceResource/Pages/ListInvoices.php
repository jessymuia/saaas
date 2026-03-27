<?php

namespace App\Filament\Resources\Central\InvoiceResource\Pages;

use App\Filament\Resources\Central\InvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;
}