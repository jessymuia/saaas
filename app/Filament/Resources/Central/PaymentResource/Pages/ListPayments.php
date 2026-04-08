<?php

namespace App\Filament\Resources\Central\PaymentResource\Pages;

use App\Filament\Resources\Central\PaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
