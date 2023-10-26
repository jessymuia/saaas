<?php

namespace App\Filament\Resources\VacationNoticeResource\Pages;

use App\Filament\Resources\VacationNoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVacationNotice extends ViewRecord
{
    protected static string $resource = VacationNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
