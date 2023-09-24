<?php

namespace App\Filament\Resources\UtilityResource\Pages;

use App\Filament\Resources\UtilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUtility extends ViewRecord
{
    protected static string $resource = UtilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
