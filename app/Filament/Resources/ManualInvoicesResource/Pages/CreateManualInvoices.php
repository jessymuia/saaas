<?php

namespace App\Filament\Resources\ManualInvoicesResource\Pages;

use App\Filament\Resources\ManualInvoicesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManualInvoices extends CreateRecord
{
    protected static string $resource = ManualInvoicesResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
