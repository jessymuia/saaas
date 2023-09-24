<?php

namespace App\Filament\Resources\ServicesOfferedResource\Pages;

use App\Filament\Resources\ServicesOfferedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicesOffereds extends ListRecords
{
    protected static string $resource = ServicesOfferedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
