<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // custom action
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
                ->requiresConfirmation(),
        ];
    }
}
