<?php

namespace App\Filament\Resources\UtilityResource\Pages;

use App\Filament\Resources\UtilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUtility extends CreateRecord
{
    protected static string $resource = UtilityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}
