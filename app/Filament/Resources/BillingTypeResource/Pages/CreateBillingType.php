<?php

namespace App\Filament\Resources\BillingTypeResource\Pages;

use App\Filament\Resources\BillingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingType extends CreateRecord
{
    protected static string $resource = BillingTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}
