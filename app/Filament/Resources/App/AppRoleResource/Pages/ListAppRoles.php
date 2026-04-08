<?php

namespace App\Filament\Resources\App\AppRoleResource\Pages;

use App\Filament\Resources\App\AppRoleResource;
use Filament\Resources\Pages\ListRecords;

class ListAppRoles extends ListRecords
{
    protected static string $resource = AppRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
