<?php

namespace App\Filament\Resources\App\PropertyOwnersResource\Pages;

use App\Filament\Resources\App\PropertyOwnersResource;
use Filament\Resources\Pages\ListRecords;

class ListPropertyOwners extends ListRecords
{
    protected static string $resource = PropertyOwnersResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
