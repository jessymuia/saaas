<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Filament\Resources\Central\SaasClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSaasClients extends ListRecords
{
    protected static string $resource = SaasClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}