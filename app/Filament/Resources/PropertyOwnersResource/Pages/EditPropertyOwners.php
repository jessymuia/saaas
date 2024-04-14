<?php

namespace App\Filament\Resources\PropertyOwnersResource\Pages;

use App\Filament\Resources\PropertyOwnersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyOwners extends EditRecord
{
    protected static string $resource = PropertyOwnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
