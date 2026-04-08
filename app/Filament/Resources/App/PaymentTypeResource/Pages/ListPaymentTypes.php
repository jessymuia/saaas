<?php

namespace App\Filament\Resources\App\PaymentTypeResource\Pages;

use App\Filament\Resources\App\PaymentTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTypes extends ListRecords
{
    protected static string $resource = PaymentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
