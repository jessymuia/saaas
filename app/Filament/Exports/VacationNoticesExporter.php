<?php

namespace App\Filament\Exports;

use App\Models\VacationNotices;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class VacationNoticesExporter extends Exporter
{
    protected static ?string $model = VacationNotices::class;

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
            ExportColumn::make('property_id'),
            ExportColumn::make('notice_start_date'),
            ExportColumn::make('notice_end_date'),
            ExportColumn::make('extra_information'),
            ExportColumn::make('document_url'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your vacation notices export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
