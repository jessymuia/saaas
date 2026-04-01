<?php

namespace App\Filament\Resources\App\ManualInvoicesResource\Pages;

use App\Filament\Resources\App\ManualInvoicesResource;
use Filament\Resources\Pages\ListRecords;

class ListManualInvoices extends ListRecords
{
    protected static string $resource = ManualInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
