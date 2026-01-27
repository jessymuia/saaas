<?php

namespace App\Filament\Resources\ManualInvoicesResource\Pages;

use App\Filament\Resources\ManualInvoicesResource;
use App\Models\ManualInvoices;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use App\Filament\Widgets\ManualInvoicesStatsOverview;

class ListManualInvoices extends ListRecords
{
    protected static string $resource = ManualInvoicesResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            ManualInvoicesStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generate-manual-invoice-documents')
                ->label('Generate Invoice Documents')
                ->action(function (){
                    // get all invoices without a document
                    ManualInvoices::query()
                        ->where('is_generated', false)
                        ->where('is_confirmed', true)
                        ->chunk(1000, function ($invoices){
                            $allSuccess = true;
                            foreach ($invoices as $invoice){
                                // generate the docs
                                try {
                                    $invoice->generateDocument($invoice);
                                }catch (\Exception $e){
                                    // log the error
                                    Log::error($e->getMessage());
                                    Log::error($e->getTraceAsString());
                                    Log::error("Failed to generate document for invoice: {$invoice->id}");
                                    Log::error("_____________________________________________________________________________");
                                    $allSuccess = false;
                                    continue;
                                }
                            }
                            if ($allSuccess){
                                Notification::make()
                                    ->success()
                                    ->title('All documents generated successfully')
                                    ->send();
                            }else{
                                Notification::make()
                                    ->danger()
                                    ->title('Some documents failed to generate')
                                    ->send();
                            }
                        });
                }),
        ];
    }
}
