<?php

namespace App\Filament\Resources\App\InvoiceResource\Pages;

use App\Filament\Resources\App\InvoiceResource;
use App\Models\Invoice;
use App\Utils\AppPermissions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateInvoiceDocuments')
                ->label('Generate Invoice Documents')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Invoice Documents')
                ->modalDescription('This will generate PDF documents for all invoices that have not yet been generated for your account.')
                ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_INVOICE_PDF))
                ->action(function () {
                    $tenantId = filament()->getTenant()?->id;
                    $invoices = Invoice::where('saas_client_id', $tenantId)
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
                            ->title("{$success} invoice document(s) generated successfully" . ($failed > 0 ? ", {$failed} failed." : '.'))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title($failed > 0 ? "{$failed} invoice(s) could not be generated." : 'All invoice documents are already generated.')
                            ->warning()
                            ->send();
                    }
                }),

            Action::make('sendInvoiceDocuments')
                ->label('Send Invoice Documents')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Send Invoice Documents')
                ->modalDescription('This will email all generated invoice documents to the respective tenants.')
                ->action(function () {
                    $tenantId = filament()->getTenant()?->id;
                    $invoices = Invoice::where('saas_client_id', $tenantId)
                        ->where('is_generated', true)
                        ->get();

                    if ($invoices->isEmpty()) {
                        Notification::make()
                            ->title('No generated invoices found to send. Please generate documents first.')
                            ->warning()
                            ->send();
                        return;
                    }

                    foreach ($invoices as $invoice) {
                        try {
                            $invoice->sendInvoiceMail();
                        } catch (\Throwable) {
                        }
                    }

                    Notification::make()
                        ->title("{$invoices->count()} invoice(s) queued for sending.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
