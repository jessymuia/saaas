<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditNotes extends ListRecords
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
            Actions\Action::make('generate-credit-note-documents')
                ->label('Generate Credit Note Documents')
                ->icon('heroicon-o-document-text')
                ->action(function(){
                    CreditNote::query()
                        ->where('is_confirmed', true)
                        ->where('is_document_generated', false)
                        ->chunk(1000, function ($creditNotes) {
                            foreach ($creditNotes as $creditNote) {
                                $creditNote->generateCreditNoteDocument();
                            }
                        });
                }),
        ];
    }
}
