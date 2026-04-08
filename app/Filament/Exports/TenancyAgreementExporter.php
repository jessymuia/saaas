<?php

namespace App\Filament\Exports;

use App\Models\TenancyAgreement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TenancyAgreementExporter extends Exporter
{
    protected static ?string $model = TenancyAgreement::class;

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
            ExportColumn::make('unit_id'),
            ExportColumn::make('tenant_id'),
            ExportColumn::make('agreement_type_id'),
            ExportColumn::make('billing_type_id'),
            ExportColumn::make('start_date'),
            ExportColumn::make('end_date'),
            ExportColumn::make('amount'),
            ExportColumn::make('deposit_amount'),
            ExportColumn::make('escalation_rate'),
            ExportColumn::make('escalation_period_in_months'),
            ExportColumn::make('next_escalation_date'),
            ExportColumn::make('balance_carried_forward'),
            ExportColumn::make('has_invoice_for_balance_carried_forward'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your tenancy agreement export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
