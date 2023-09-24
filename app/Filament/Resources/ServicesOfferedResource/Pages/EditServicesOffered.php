<?php

namespace App\Filament\Resources\ServicesOfferedResource\Pages;

use App\Filament\Resources\ServicesOfferedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicesOffered extends EditRecord
{
    protected static string $resource = ServicesOfferedResource::class;

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
