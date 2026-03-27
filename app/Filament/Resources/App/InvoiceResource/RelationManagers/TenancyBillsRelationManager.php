<?php

namespace App\Filament\Resources\App\InvoiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema; // v4 specific
use Filament\Tables\Columns\TextColumn;

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // Add fields if you want to edit bills directly from the invoice
            \Filament\Forms\Components\TextInput::make('amount')
                ->numeric()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bill_type.name')->label('Type'),
                TextColumn::make('amount')->money('kes'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
