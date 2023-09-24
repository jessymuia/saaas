<?php

namespace App\Filament\Resources\RentPaymentResource\Pages;

use App\Filament\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRentPayment extends CreateRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;
        $data['received_by'] = auth()->user()->id;

        return $data;
    }
}
