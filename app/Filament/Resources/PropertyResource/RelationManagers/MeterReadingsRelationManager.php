<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Models\MeterReading;
use App\Models\PropertyUtility;
use App\Rules\CheckValidCurrentReadingInput;
use App\Rules\CheckValidReadingDateMeterReading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeterReadingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meterReadings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->reactive()
                    ->relationship('unit', 'name', fn (Builder $query) => $query->where('property_id', $this->ownerRecord->id))
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        $meterReading = MeterReading::query()->where('unit_id', $get('unit_id'))
                            ->where('utility_id', $get('utility_id'))
                            ->orderBy('reading_date', 'desc')
                            ->first();
                        $set('previous_reading', $meterReading->current_reading ?? 0);
                    })
                    ->required(),
                Forms\Components\Select::make('utility_id')
                    ->label('Utility')
                    ->reactive()
                    ->relationship('utility', 'name', function (Builder $query) {
                        $query->whereHas('propertyUtilities', function (Builder $query) {
                            $query->where('property_id', $this->ownerRecord->id);
                        });
                    })
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        $meterReading = MeterReading::query()->where('unit_id', $get('unit_id'))
                            ->where('utility_id', $get('utility_id'))
                            ->orderBy('reading_date', 'desc')
                            ->first();
                        $set('previous_reading', $meterReading->current_reading ?? 0);
                    })
                    ->required(),
                Forms\Components\DatePicker::make('reading_date')
                    ->label('Reading Date')
                    ->reactive()
                    ->rules([
                        // check if the page is currently being edited
                        fn(Get $get) : CheckValidReadingDateMeterReading => new CheckValidReadingDateMeterReading(
                            $get('unit_id'),
                            $get('utility_id'),
                            $form->getOperation()
                        ),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('current_reading')
                    ->required()
                    ->numeric()
                    ->rules([
                        fn(Get $get) : CheckValidCurrentReadingInput => new CheckValidCurrentReadingInput(
                            $get('previous_reading')
                        ),
                    ])
                    ->minValue(function (Forms\Get $get) {
                        return $get('current_reading');
                    }),
                Forms\Components\TextInput::make('previous_reading')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->readOnly(function (Forms\Get $get) {
//                        return MeterReading::query()->where('unit_id', $get('unit_id'))
//                            ->where('utility_id', $get('utility_id'))
//                            ->count() > 0;
//                        TODO: FLAG:MIGRATION
                    })
                    ->minValue(0)

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
                Tables\Columns\TextColumn::make('utility.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reading_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_reading')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('previous_reading')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('consumption')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_bill')
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
                    $data['consumption'] = $data['current_reading'] - $data['previous_reading'];

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
                    ->requiresConfirmation('Are you sure you want to delete this record?')
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->user()->id;

                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->mutateFormDataUsing(function ($data) {
                            $data['deleted_by'] = auth()->user()->id;

                            return $data;
                        }),
                ]),
            ]);
    }
}
