<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\SentEmails;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('Generate Document')
                ->label(function ($record) {
                    return $record->is_generated ? 'Regenerate Document' : 'Generate Document';
                })
                ->action(function ($record) {
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
            Actions\Action::make('view-docoument')
                ->label('View Document')
                ->url(function ($record) {
                    if (!$record->is_generated) {
                        return route('preview.invoice',['invoice'=>null]);
                    }
                    $fileName = str_replace('invoices/','',$record->document_url);
                    return route('preview.invoice',['invoice'=>$fileName]);
                })
                ->visible(function ($record) {
                    return $record->is_generated;
                }),
            Actions\Action::make('Confirm Invoice')
                ->action(function ($record) {
                    $record->is_confirmed = true;
                    $record->save();

                    Notification::make('invoiceConfirmation')
                        ->title('Invoice Confirmed')
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.invoices.view', ['record' => $record->id]);
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

    protected function getHeaderWidgets(): array
    {
        return [
            //
//            InvoiceResource\Widgets\InvoiceViewStats::class,
            InvoiceResource\Widgets\InvoiceDetailsWidget::class,
        ];
    }
}
