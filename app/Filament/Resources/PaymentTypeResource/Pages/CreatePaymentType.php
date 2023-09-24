<?php

namespace App\Filament\Resources\PaymentTypeResource\Pages;

use App\Filament\Resources\PaymentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentType extends CreateRecord
{
    protected static string $resource = PaymentTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}
