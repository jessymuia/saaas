<?php

namespace App\Filament\Resources\ManualInvoicesResource\Pages;

use App\Filament\Resources\ManualInvoicesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManualInvoices extends EditRecord
{
    protected static string $resource = ManualInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['deleted_by'] = auth()->id();

                    return $data;
                }),
        ];
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
