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
            TextInput::make('name')
                ->label('Credit Note Name')
                ->required()
                ->maxLength(500),

            TextInput::make('amount_credited')
                ->label('Amount Credited (KES)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->prefix('KES'),

            \Filament\Forms\Components\Textarea::make('reason_for_issuance')
                ->label('Reason for Issuance')
                ->nullable()
                ->rows(3)
                ->columnSpanFull(),

            \Filament\Forms\Components\Textarea::make('notes')
                ->label('Additional Notes')
                ->nullable()
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['saas_client_id'] = $this->getOwnerRecord()->saas_client_id;
        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Credit Note')->searchable(),
                TextColumn::make('amount_credited')->label('Amount Credited')->money('KES')->sortable(),
                TextColumn::make('reason_for_issuance')->label('Reason')->limit(50),
                TextColumn::make('is_confirmed')->label('Confirmed')->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ]);
    }
}
