<?php

namespace App\Filament\Resources\EscalationRatesAndAmountsLogsResource\Pages;

use App\Filament\Resources\EscalationRatesAndAmountsLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEscalationRatesAndAmountsLogs extends EditRecord
{
    protected static string $resource = EscalationRatesAndAmountsLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
