<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use  App\Filament\Resources\PropertyResource\Widget\PropertyStats;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'isAggregate' => true,
        ];
    }
}
