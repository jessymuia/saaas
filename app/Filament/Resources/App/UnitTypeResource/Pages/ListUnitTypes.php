<?php

namespace App\Filament\Resources\App\UnitTypeResource\Pages;

use App\Filament\Resources\App\UnitTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListUnitTypes extends ListRecords
{
    protected static string $resource = UnitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
