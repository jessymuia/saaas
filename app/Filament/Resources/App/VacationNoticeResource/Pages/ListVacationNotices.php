<?php

namespace App\Filament\Resources\App\VacationNoticeResource\Pages;

use App\Filament\Resources\App\VacationNoticeResource;
use Filament\Resources\Pages\ListRecords;

class ListVacationNotices extends ListRecords
{
    protected static string $resource = VacationNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
