<?php

namespace App\Filament\Resources\UnitTypeResource\Pages;

use App\Filament\Resources\UnitTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitType extends ViewRecord
{
    protected static string $resource = UnitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
