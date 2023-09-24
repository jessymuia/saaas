<?php

namespace App\Filament\Resources\ServicesOfferedResource\Pages;

use App\Filament\Resources\ServicesOfferedResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServicesOffered extends CreateRecord
{
    protected static string $resource = ServicesOfferedResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }
}
