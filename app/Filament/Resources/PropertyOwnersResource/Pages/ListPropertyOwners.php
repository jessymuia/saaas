<?php

namespace App\Filament\Resources\PropertyOwnersResource\Pages;

use App\Filament\Resources\PropertyOwnersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyOwners extends ListRecords
{
    protected static string $resource = PropertyOwnersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
