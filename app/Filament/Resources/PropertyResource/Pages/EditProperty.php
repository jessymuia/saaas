<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Widgets\PropertyStats;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
     protected function getHeaderWidgets(): array
    {
        return [
            PropertyStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm'      => 2,
            'md'      => 3,
            'lg'      => 4,
        ];
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->user()->id;

        return $data;
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
