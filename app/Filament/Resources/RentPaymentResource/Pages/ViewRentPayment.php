<?php

namespace App\Filament\Resources\RentPaymentResource\Pages;

use App\Filament\Resources\RentPaymentResource;
use App\Models\RentPayment;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Not;

class ViewRentPayment extends ViewRecord
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        $headerActions = [];

        $headerActions[] = Actions\EditAction::make();

        if ($this->getRecord()->is_confirmed == 0){
            $headerActions[] = Actions\Action::make('confirm-rent-payment')
                ->label("Confirm Rent Payment")
                ->action(function(){
                    // update the is_confirmed label of this rent payment
                    $isUpdated = $this->getRecord()->update(['is_confirmed' => true]);

                    if ($isUpdated){
                        Notification::make()
                            ->title('Rent Payment Confirmed')
                            ->success()
                            ->send();
                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Rent Payment Confirmation Failed')
                            ->send();
                    }
                });
        }else if ($this->getRecord()->is_confirmed == 1  && strtotime($this->getRecord()->document_generated_at) === false){
            $headerActions[] = Actions\Action::make('reject-rent-payment')
                ->label("Deny Rent Payment")
                ->color('danger')
                ->action(function(){
                    // update the is_confirmed label of this rent payment
                    $isUpdated = $this->getRecord()->update(['is_confirmed' => false]);

                    if ($isUpdated){
                        Notification::make()
                            ->title('Rent Payment rejected successfully')
                            ->success()
                            ->send();
                    }else{
                        Notification::make()
                            ->danger()
                            ->title('Rent Payment rejection Failed')
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
