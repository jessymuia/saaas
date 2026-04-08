<?php

namespace App\Filament\Resources\App\PropertyTypeResource\Pages;

use App\Filament\Resources\App\PropertyTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListPropertyTypes extends ListRecords
{
    protected static string $resource = PropertyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
