<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // generate document action
            Actions\Action::make('generate-invoice-documents')
                ->label('Generate Invoice Documents')
                ->action(function (){
                    // get all invoices without a document
                    Invoice::query()
                        ->where('is_generated', false)
                        ->where('is_confirmed', true)
                        ->chunk(1000, function ($invoices){
                            foreach ($invoices as $invoice){
                                Log::info("Generating document for invoice: {$invoice->id}");
                                // generate the docs
                                try {
                                    $invoice->generateDocument($invoice);
                                }catch (\Exception $e){
                                    // log the error
                                    Log::error($e->getMessage());
                                    continue;
                                }
                            }
                        });
                })
        ];
    }
}
