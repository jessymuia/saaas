<?php

namespace App\Filament\Resources\TenancyAgreementTypeResource\Pages;

use App\Filament\Resources\TenancyAgreementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenancyAgreementType extends CreateRecord
{
    protected static string $resource = TenancyAgreementTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}
