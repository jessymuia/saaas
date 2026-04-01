<?php

namespace App\Filament\Resources\App\UtilityResource\Pages;

use App\Filament\Resources\App\UtilityResource;
use Filament\Resources\Pages\ListRecords;

class ListUtilities extends ListRecords
{
    protected static string $resource = UtilityResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
