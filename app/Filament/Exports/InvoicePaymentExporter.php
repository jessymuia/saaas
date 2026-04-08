<?php

namespace App\Filament\Exports;

use App\Models\InvoicePayment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoicePaymentExporter extends Exporter
{
    protected static ?string $model = InvoicePayment::class;

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
            ExportColumn::make('invoice_id'),
            ExportColumn::make('tenant_id'),
            ExportColumn::make('client_id'),
            ExportColumn::make('property_owner_id'),
            ExportColumn::make('payment_type_id'),
            ExportColumn::make('received_by'),
            ExportColumn::make('payment_date'),
            ExportColumn::make('amount'),
            ExportColumn::make('paid_by'),
            ExportColumn::make('payment_reference'),
            ExportColumn::make('description'),
            ExportColumn::make('document_path'),
            ExportColumn::make('document_generated_at'),
            ExportColumn::make('document_generated_by'),
            ExportColumn::make('is_confirmed'),
            ExportColumn::make('document_sent_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your invoice payment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
