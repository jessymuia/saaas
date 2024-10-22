<?php

namespace App\Filament\Exports;

use App\Models\CreditNote;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CreditNoteExporter extends Exporter
{
    protected static ?string $model = CreditNote::class;

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
            ExportColumn::make('name'),
            ExportColumn::make('invoice_id'),
            ExportColumn::make('issue_date'),
            ExportColumn::make('reason_for_issuance'),
            ExportColumn::make('amount_credited'),
            ExportColumn::make('notes'),
            ExportColumn::make('document_url'),
            ExportColumn::make('is_confirmed'),
            ExportColumn::make('is_document_generated'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your credit note export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
