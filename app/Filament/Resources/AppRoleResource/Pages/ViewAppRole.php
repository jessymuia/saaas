<?php

namespace App\Filament\Resources\AppRoleResource\Pages;

use App\Filament\Resources\AppRoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAppRole extends ViewRecord
{
    protected static string $resource = AppRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
            EditAction::make()
        ];
    }
}
