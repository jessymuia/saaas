<?php

namespace App\Filament\Resources\InvoicePaymentResource\Pages;

use App\Filament\Resources\InvoicePaymentResource;
use App\Models\InvoicePayment;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Not;

class ViewInvoicePayment extends ViewRecord
{
    protected static string $resource = InvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        $headerActions = [];

        $headerActions[] = Actions\EditAction::make()
            ->visible(fn (InvoicePayment $record) => $record->is_confirmed == 0);

        if ($this->getRecord()->is_confirmed == 0){
            $headerActions[] = Actions\Action::make('confirm-rent-payment')
                ->label("Confirm Invoice Payment")
                ->action(function(){
                    // update the is_confirmed label of this Invoice Payment
                    $isUpdated = $this->getRecord()->update(['is_confirmed' => true]);

                    if ($isUpdated){
                        Notification::make()
                            ->title('Invoice Payment Confirmed')
                            ->success()
                            ->send();
                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Invoice Payment Confirmation Failed')
                            ->send();
                    }
                });
        }else if ($this->getRecord()->is_confirmed == 1  && strtotime($this->getRecord()->document_generated_at) === false){
            $headerActions[] = Actions\Action::make('reject-rent-payment')
                ->label("Deny Invoice Payment")
                ->color('danger')
                ->action(function(){
                    // update the is_confirmed label of this Invoice Payment
                    $isUpdated = $this->getRecord()->update(['is_confirmed' => false]);

                    if ($isUpdated){
                        Notification::make()
                            ->title('Invoice Payment rejected successfully')
                            ->success()
                            ->send();
                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Invoice Payment rejection Failed')
                            ->send();
                    }
                });
        }

        // check if the document has been generated
        if (strtotime($this->getRecord()->is_confirmed == 1 && $this->getRecord()->document_generated_at) === false){
            $headerActions[] = Actions\Action::make('generate-receipt')
                ->label("Generate Receipt")
                ->icon('heroicon-o-document-text')
                ->action(function(){
                    // TODO: Logic to generate the receipt
                }
            );
        }

        return $headerActions;
    }
}
