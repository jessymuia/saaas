<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Rules\CheckOccupancyOfUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenancyAgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyAgreements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->required()
                    ->rules([
                        fn(Get $get) : CheckOccupancyOfUnit => new CheckOccupancyOfUnit($get('start_date')),
                    ])
                    ->relationship('unit', 'name'),
                Forms\Components\Select::make('billing_type_id')
                    ->label('Billing Type')
                    ->required()
                    ->relationship('billingType', 'type'),
                Forms\Components\Select::make('agreement_type_id')
                    ->label('Agreement Type')
                    ->required()
                    ->relationship('agreementType', 'type'),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->after('start_date'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agreementType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['created_by'] = auth()->user()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['updated_by'] = auth()->user()->id;
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->user()->id;
                        return $data;
                    })
                    ->requiresConfirmation(fn ($record) => 'Are you sure you want to delete this tenancy agreement?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->mutateFormDataUsing(function ($data) {
                            $data['deleted_by'] = auth()->user()->id;
                            return $data;
                        })
                        ->requiresConfirmation(fn ($records) => 'Are you sure you want to delete the selected tenancy agreements?'),
                ]),
            ]);
    }
}
