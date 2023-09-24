<?php

namespace App\Filament\Resources\TenancyAgreementTypeResource\Pages;

use App\Filament\Resources\TenancyAgreementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenancyAgreementTypes extends ListRecords
{
    protected static string $resource = TenancyAgreementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
