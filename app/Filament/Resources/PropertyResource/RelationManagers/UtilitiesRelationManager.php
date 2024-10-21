<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Rules\CheckUniqueUtilityInProperty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UtilitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'utilities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('utility_id')
                    ->label('Utility')
                    ->required()
                    ->rules([
                        fn(Get $get) : CheckUniqueUtilityInProperty => new CheckUniqueUtilityInProperty(
                            $this->ownerRecord->id,$form->getOperation()
                        ),
                    ])
                    ->disabledOn('edit')
                    ->relationship('utility', 'name'),
                Forms\Components\Select::make('billing_type_id')
                    ->label('Billing Type')
                    ->required()
                    ->relationship('billingType', 'type'),
                Forms\Components\TextInput::make('rate_per_unit')
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
                Tables\Columns\TextColumn::make('utility.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate_per_unit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
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
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['updated_by'] = auth()->id();
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation('Are you sure you want to delete this utility?')
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UtilitiesRelationManager::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UtilitiesRelationManager::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
