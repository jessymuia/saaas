<?php

namespace App\Filament\Resources\VacationNoticeResource\Pages;

use App\Filament\Resources\VacationNoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVacationNotice extends EditRecord
{
    protected static string $resource = VacationNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->user()->id;

        return $data;
    }
}
