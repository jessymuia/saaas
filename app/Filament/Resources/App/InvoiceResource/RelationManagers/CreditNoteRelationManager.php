<?php

namespace App\Filament\Resources\App\InvoiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class CreditNoteRelationManager extends RelationManager
{
    protected static string $relationship = 'creditNotes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')
                ->numeric()
                ->required()
                ->prefix('KES'),
            TextInput::make('reason')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->money('kes'),
                TextColumn::make('reason'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ]);
    }
}
