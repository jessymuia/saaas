<?php

namespace App\Filament\Exports;

use App\Models\PropertyServices;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PropertyServicesExporter extends Exporter
{
    protected static ?string $model = PropertyServices::class;

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
            ExportColumn::make('property_id'),
            ExportColumn::make('service_id'),
            ExportColumn::make('rate'),
            ExportColumn::make('billing_type_id'),
            ExportColumn::make('is_deleted'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your property services export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
