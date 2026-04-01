<?php

namespace App\Filament\Resources\App\InvoiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;

class InvoicePaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paymentType.type')->label('Payment Type')->sortable(),
                TextColumn::make('amount')->money('KES')->sortable(),
                TextColumn::make('paid_by')->label('Paid By')->searchable(),
                TextColumn::make('payment_date')->dateTime()->sortable()->label('Date'),
                TextColumn::make('payment_reference')->label('Reference')->searchable(),
                TextColumn::make('is_confirmed')->label('Confirmed')->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
            ])
            ->headerActions([])
            ->actions([]);
    }
}
