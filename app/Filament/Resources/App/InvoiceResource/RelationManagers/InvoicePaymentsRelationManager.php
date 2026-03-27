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
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('amount')
                ->numeric()
                ->required(),
            \Filament\Forms\Components\Select::make('payment_method')
                ->options([
                    'mpesa' => 'M-Pesa',
                    'bank' => 'Bank Transfer',
                    'cash' => 'Cash',
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->money('kes'),
                TextColumn::make('payment_method')->badge(),
                TextColumn::make('created_at')->label('Paid Date')->date(),
            ]);
    }
}
