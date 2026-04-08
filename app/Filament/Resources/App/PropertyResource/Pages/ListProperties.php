<?php

namespace App\Filament\Resources\App\PropertyResource\Pages;

use App\Filament\Resources\App\PropertyResource;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
