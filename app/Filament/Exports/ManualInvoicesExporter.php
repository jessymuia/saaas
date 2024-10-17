<?php

namespace App\Filament\Exports;

use App\Models\ManualInvoices;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ManualInvoicesExporter extends Exporter
{
    protected static ?string $model = ManualInvoices::class;

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
            ExportColumn::make('property_owner_id'),
            ExportColumn::make('client_id'),
            ExportColumn::make('tenant_id'),
            ExportColumn::make('comments'),
            ExportColumn::make('invoice_status'),
            ExportColumn::make('issue_date'),
            ExportColumn::make('invoice_for_month'),
            ExportColumn::make('invoice_due_date'),
            ExportColumn::make('is_confirmed'),
            ExportColumn::make('is_generated'),
            ExportColumn::make('document_url'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your manual invoices export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
