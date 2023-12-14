<?php

namespace App\Filament\Resources\SentEmailsResource\Pages;

use App\Filament\Resources\SentEmailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSentEmails extends ListRecords
{
    protected static string $resource = SentEmailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
