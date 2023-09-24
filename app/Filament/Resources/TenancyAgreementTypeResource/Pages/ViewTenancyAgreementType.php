<?php

namespace App\Filament\Resources\TenancyAgreementTypeResource\Pages;

use App\Filament\Resources\TenancyAgreementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTenancyAgreementType extends ViewRecord
{
    protected static string $resource = TenancyAgreementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
