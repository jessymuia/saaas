<?php

namespace App\Filament\Resources\InvoicePaymentResource\Pages;

use App\Filament\Resources\InvoicePaymentResource;
use App\Models\InvoicePayment;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListInvoicePayments extends ListRecords
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // custom action to send out mails
            Actions\Action::make('send-receipts')
                ->label("Send Receipts")
                ->icon('heroicon-o-envelope')
                ->action(function(){
                    $allEmailsSentSuccessfully = true;
                    // get all the invoice payments that have not been confirmed
                    InvoicePayment::whereNotNull('document_path')
                        ->whereNull('document_sent_at')
                        ->chunk(100, function ($invoicePayments) {
                            // loop through the invoice payments and send out mails
                            foreach ($invoicePayments as $invoicePayment){
                                // send out mail
                                $sentStatus = $invoicePayment->sendInvoicePaymentEmail();
                                if (!$sentStatus){
                                    $allEmailsSentSuccessfully = false;
                                }
                            }
                        });

                    if ($allEmailsSentSuccessfully) {
                        Notification::make()
                            ->title('Receipts sent out successfully')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Not all receipts were sent successfully')
                            ->send();
                    }
                })
        ];
    }
}
