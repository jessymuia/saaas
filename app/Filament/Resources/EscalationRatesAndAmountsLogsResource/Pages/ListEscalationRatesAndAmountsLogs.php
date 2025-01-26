<?php

namespace App\Filament\Resources\EscalationRatesAndAmountsLogsResource\Pages;

use App\Filament\Resources\EscalationRatesAndAmountsLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEscalationRatesAndAmountsLogs extends ListRecords
{
    protected static string $resource = EscalationRatesAndAmountsLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
