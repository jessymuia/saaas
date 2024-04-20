<?php

namespace App\Filament\Resources\ManualInvoicesResource\Pages;

use App\Filament\Resources\ManualInvoicesResource;
use App\Models\ManualInvoices;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

class ViewManualInvoices extends ViewRecord
{
    protected static string $resource = ManualInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('View Document')
                ->label('View Document')
                ->icon('heroicon-o-document-text')
                ->disabled(fn (ManualInvoices $invoice) => !$invoice->is_generated)
                ->url(function (ManualInvoices $invoice) {
                    if (!$invoice->is_generated) {
                        return route('preview.manual-invoice',['invoice'=>null]);
                    }
                    $fileName = str_replace('manual_invoices/','',$invoice->document_url);
                    return route('preview.manual-invoice',['invoice'=>$fileName]);
                }),
            Actions\Action::make('Generate Document')
                ->label(function ($record) {
                    return $record->is_generated ? 'Regenerate Document' : 'Generate Document';
                })
                ->action(function ($record) {
                    Log::info("Generating document for invoice: {$record->id}");
                    try {
                        if ($record->is_generated){
                            $record->generateDocument($record,true);
                        }else{
                            $record->generateDocument($record);
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        Log::error($e->getTraceAsString());
                        Log::error("Failed to generate document for invoice: {$record->id}");
                        Log::error("_____________________________________________________________________________");
                        Notification::make()
                            ->danger()
                            ->title('Failed to generate document')
                            ->send();
                    }
                    Notification::make()
                        ->success()
                        ->title('Document generated successfully')
                        ->send();
                }),
            // custom action
            Actions\Action::make('Confirm Invoice')
                ->action(function ($record) {
                    $record->is_confirmed = true;
                    $record->save();

                    Notification::make('invoiceConfirmation')
                        ->title('Invoice Confirmed')
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.manualinvoices.view', ['record' => $record->id]);
                })
                ->visible(function ($record) {
                    return !$record->is_confirmed;
                })
                ->requiresConfirmation(),
            // action to sent out email if the invoice is confirmed and generated
            Actions\Action::make('Send Invoice')
                ->label(function ($record) {
//                    return $record->issue_date ? 'Invoice Sent' : 'Send Invoice';
                    return $record->invoiceIsSent() ? 'Invoice Sent' : 'Send Invoice';
                })
                ->action(function ($record) {
                    // send email
                    if($record->sendInvoiceMail()){
                        Notification::make('invoiceSent')
                            ->title('Invoice Sent')
                            ->success()
                            ->send();
                    }else{
                        Notification::make('invoiceSent')
                            ->title('Failed sending invoice')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(function ($record) {
                    return $record->is_confirmed && $record->is_generated;
                })
                ->disabled(function ($record) {
//                    return $record->is_confirmed && $record->is_generated && $record->issue_date;
                    return $record->is_confirmed && $record->is_generated
                        && $record->invoiceIsSent();
                })
                ->requiresConfirmation(),
        ];
    }
}
