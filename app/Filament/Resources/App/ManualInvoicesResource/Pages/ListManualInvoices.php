<?php

namespace App\Filament\Resources\App\ManualInvoicesResource\Pages;

use App\Filament\Resources\App\ManualInvoicesResource;
use App\Models\ManualInvoices;
use App\Utils\AppPermissions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListManualInvoices extends ListRecords
{
    protected static string $resource = ManualInvoicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Manual Invoice'),

            Action::make('generateManualInvoiceDocuments')
                ->label('Generate Invoice Documents')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Manual Invoice Documents')
                ->modalDescription('This will generate PDF documents for all manual invoices that have not yet been generated for your account.')
                ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_MANUAL_INVOICE_PDF))
                ->action(function () {
                    $tenantId = filament()->getTenant()?->id;
                    $invoices = ManualInvoices::where('saas_client_id', $tenantId)
                        ->where('is_generated', false)
                        ->get();

                    $success = 0;
                    $failed  = 0;

                    foreach ($invoices as $invoice) {
                        try {
                            if ($invoice->generateDocument($invoice)) {
                                $success++;
                            } else {
                                $failed++;
                            }
                        } catch (\Throwable) {
                            $failed++;
                        }
                    }

                    if ($success > 0) {
                        Notification::make()
                            ->title("{$success} manual invoice document(s) generated successfully" . ($failed > 0 ? ", {$failed} failed." : '.'))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title($failed > 0 ? "{$failed} invoice(s) could not be generated." : 'All manual invoice documents are already generated.')
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
}
