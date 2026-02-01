<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use  App\Filament\Resources\PropertyResource\Widget\PropertyStats;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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

    
    protected function getHeaderWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
