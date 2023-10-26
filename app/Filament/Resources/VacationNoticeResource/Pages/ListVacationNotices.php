<?php

namespace App\Filament\Resources\VacationNoticeResource\Pages;

use App\Filament\Resources\VacationNoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVacationNotices extends ListRecords
{
    protected static string $resource = VacationNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
