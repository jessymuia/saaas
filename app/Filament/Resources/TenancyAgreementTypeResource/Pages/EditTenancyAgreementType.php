<?php

namespace App\Filament\Resources\TenancyAgreementTypeResource\Pages;

use App\Filament\Resources\TenancyAgreementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenancyAgreementType extends EditRecord
{
    protected static string $resource = TenancyAgreementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->user()->id;

        return $data;
    }
}
