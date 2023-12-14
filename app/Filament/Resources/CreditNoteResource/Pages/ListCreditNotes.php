<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Filament\Actions;
use Filament\Notifications\Notification;
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
            // send generated credit note documents
            Actions\Action::make('send-generated-credit-notes')
                ->label('Mail Credit Notes')
                ->icon('heroicon-o-envelope')
                ->action(function() {
                    CreditNote::query()
                        ->where('is_document_generated',true)
                        ->where('issue_date',null)
                        ->chunk(1000,function ($creditNotes){
                            $allSendSuccessfully = true;

                            foreach ($creditNotes as $creditNote){
                                $status = $creditNote->sendCreditNoteEmail();
                                if (!$status){
                                    $allSendSuccessfully = false;
                                }
                            }

                            if (!$allSendSuccessfully){
                                Notification::make()
                                    ->danger()
                                    ->title('Some emails failed to send')
                                    ->send();
                            }else{
                                Notification::make()
                                    ->success()
                                    ->title('All mails sent successfully')
                                    ->send();
                            }
                        });
                })
        ];
    }
}
