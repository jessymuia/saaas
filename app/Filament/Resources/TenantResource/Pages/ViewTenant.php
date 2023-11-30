<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //
            TenantResource\Widgets\TenantPaymentsDueWidget::class,
//            TenantResource\Widgets\TenantLedger::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //
        ];
    }
}
