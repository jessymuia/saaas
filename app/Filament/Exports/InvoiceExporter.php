<?php

namespace App\Filament\Exports;

use App\Models\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('status'),
            ExportColumn::make('archive'),
            ExportColumn::make('created_by'),
            ExportColumn::make('updated_by'),
            ExportColumn::make('deleted_by'),
            ExportColumn::make('tenancy_agreement_id'),
            ExportColumn::make('comments'),
            ExportColumn::make('invoice_status'),
            ExportColumn::make('issue_date'),
            ExportColumn::make('invoice_for_month'),
            ExportColumn::make('invoice_due_date'),
            ExportColumn::make('is_confirmed'),
            ExportColumn::make('is_generated'),
            ExportColumn::make('document_url')
                ->label('Document URL')
                ->state(function (Invoice $invoice) {
                    if (!$invoice->is_generated){
                        return 'Not generated';
                    }
                    $fileName = str_replace('invoices/','',$invoice->document_url);
                    return route('preview.invoice', ['invoice' => $fileName]);
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your invoice export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
