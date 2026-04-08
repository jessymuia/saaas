<?php

namespace App\Filament\Widgets;

use App\Models\InvoicePayment;
use App\Models\Property;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // BelongsToTenant global scope is active for all three models,
        // so these counts are always scoped to the current tenant.
        // Super admins accessing the central panel must use ::withoutTenantScope().
        return [
            Stat::make('Users', User::count()),
            Stat::make('Properties managed', Property::count()),
            Stat::make('Amount collected', 'KES ' . number_format(
                InvoicePayment::sum('amount'), 2, '.', ','
            )),
        ];
    }
}