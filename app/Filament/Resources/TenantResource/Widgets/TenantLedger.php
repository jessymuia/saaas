<?php

namespace App\Filament\Resources\TenantResource\Widgets;

use App\Models\Invoice;
use App\Models\Tenant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TenantLedger extends BaseWidget
{
    protected int | string | array $columnSpan = 2;
    public function table(Table $table): Table
    {
        // fetch all tenant invoices, fetch all tenant payments, fetch all tenant credit notes
        $invoices = Invoice::query()
            ->whereHas('tenancyAgreement', function ($query) {
                $query->where('tenant_id', '=', $this->getRecord);
            });
                // merge them all together and sort according to date, appending the type of record (whether it is a payment, invoice or credit note)
        return $table
            ->query(
                // ...
                Tenant::query()
                // fetch all tenant invoices, fetch all tenant payments, fetch all tenant credit notes
                // merge them all together and sort according to date, appending the type of record (whether it is a payment, invoice or credit note)
            )
            ->columns([
                // ...
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('status'),
            ]);
    }
}
