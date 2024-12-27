<?php

namespace App\Filament\Resources\PropertyManagementUsersResource\Pages;

use App\Filament\Resources\PropertyManagementUsersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyManagementUsers extends ListRecords
{
    protected static string $resource = PropertyManagementUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
