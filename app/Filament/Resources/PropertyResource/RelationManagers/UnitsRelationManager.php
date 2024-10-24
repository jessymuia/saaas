<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Filament\Exports\PropertyExporter;
use App\Models\Unit;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique('units', 'name', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('unit_type_id')
                    ->label('Unit Type')
                    ->required()
                    ->relationship('unitType', 'type'),
                // display the area text input if the property type of this property is commercial
                Forms\Components\TextInput::make('area_in_square_feet')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->visible(function (){
                        return $this->ownerRecord->property_type_id == 1;
                    })
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unitType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('area_in_square_feet')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: function (){
                        // hide this column if the unit type is not commercial
                        return $this->ownerRecord->property_type_id != 1;
                    }),
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
                    ->requiresConfirmation('Are you sure you want to delete this unit?')
                    ->disabled(function (Unit $unit) { // prevent deletion of units with meter_readings or units that are occupied
                        // check if this unit has any tenancy_agreement
                        if ($unit->tenancyAgreements()->count() > 0) {
                            return true;
                        }
                        // check if this unit has any meter_reading
                        if ($unit->meterReadings()->count() > 0) {
                            return true;
                        }
                        return false;
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PropertyExporter::class)
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
                    ->exporter(PropertyExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
