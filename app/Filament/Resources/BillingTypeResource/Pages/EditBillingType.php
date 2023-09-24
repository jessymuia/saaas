<?php

namespace App\Filament\Resources\BillingTypeResource\Pages;

use App\Filament\Resources\BillingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingType extends EditRecord
{
    protected static string $resource = BillingTypeResource::class;

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
