<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Filament\Exports\UnitsExporter;
use App\Models\MeterReading;
use App\Models\Unit;
use App\Utils\AppPermissions;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                ExportAction::make()
                    ->exporter(UnitsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->actions([
                // custom actions
                // reset-water-meter
                Tables\Actions\Action::make('reset-water-meter')
                    ->label('Reset Water Meter')
                    ->visible(function (Unit $record) {
                        // check if this unit has water as a utility
                        return $record?->property?->utilities()
                            ->whereHas('utility', function (Builder $query) {
                                $query->where('name', 'Water');
                            })
                            ->exists() && auth()->user()->can(AppPermissions::RESET_WATER_METER_PERMISSION);
                        // need to add check in meter generation to check whether current meter reading is 0
                    })
                    ->action(function (Unit $record) {
                        try {
                            // insert a new record in the meter_readings table with the current date and 0 as the reading
                            $utility_id = $record?->property?->utilities()
                                ->whereHas('utility', function (Builder $query) {
                                    $query->where('name', 'Water');
                                })->first(['utility_id'])->utility_id;

                            // get latest reading for this utility for this unit
                            $latestMeterReading = MeterReading::select(['current_reading'])
                                ->where('utility_id',$utility_id)
                                ->where('unit_id',$record->id)
                                ->orderBy('reading_date','desc')
                                ->limit(1)
                                ->first('current_reading')['current_reading'];

                            MeterReading::create([
                                'unit_id' => $record->id,
                                'utility_id' => $utility_id,
                                'reading_date' => now(),
                                'current_reading' => 0,
                                'previous_reading' => $latestMeterReading,
                                'consumption' => 0 - $latestMeterReading,
                                'has_bill' => true,
                                'created_by' => auth()->user()->id,
                            ]);
                            Notification::make()
                                ->title('Water meter for unit: '. $record->name .' reset to 0')
                                ->success()
                                ->send();
                        }catch (\Exception $exception){
                            \Log::error("Failed creating meter reading ".
                                $exception->getFile() . " " . $exception->getLine() . "\n".
                                $exception->getMessage() . "\n" . $exception->getTraceAsString()
                            );
                            Notification::make()
                                ->title('Error resetting water meter')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation('Are you sure you want to reset the water meter for this unit to 0?'),

                // reset-electricity-meter
                Tables\Actions\Action::make('reset-electricity-meter')
                    ->label('Reset Electricity Meter')
//                    ->visible(false)
                    ->visible(function (Unit $record) {
                        // check if this unit has electricity as a utility
                        return $record?->property?->utilities()
                            ->whereHas('utility', function (Builder $query) {
                                $query->where('name', 'Electricity');
                            })
                            ->exists() && auth()->user()->can(AppPermissions::RESET_ELECTRICITY_METER_PERMISSION);
                    })
                    ->action(function (Unit $record) {
                        try {
                            // insert a new record in the meter_readings table with the current date and 0 as the reading
                            $utility_id = $record?->property?->utilities()
                                ->whereHas('utility', function (Builder $query) {
                                    $query->where('name', 'Electricity');
                                })->first(['utility_id'])->utility_id;

                            // get latest reading for this utility for this unit
                            $latestMeterReading = MeterReading::select(['current_reading'])
                                ->where('utility_id',$utility_id)
                                ->where('unit_id',$record->id)
                                ->orderBy('reading_date','desc')
                                ->limit(1)
                                ->first('current_reading')['current_reading'];

                            MeterReading::create([
                                'unit_id' => $record->id,
                                'utility_id' => $utility_id,
                                'reading_date' => now(),
                                'current_reading' => 0,
                                'previous_reading' => $latestMeterReading,
                                'consumption' => 0 - $latestMeterReading,
                                'has_bill' => true,
                                'created_by' => auth()->user()->id,
                            ]);
                            Notification::make()
                                ->title('Electricity meter for unit: '. $record->name .' reset to 0')
                                ->success()
                                ->send();
                        }catch (\Exception $exception){
                            \Log::error("Failed creating meter reading ".
                                $exception->getFile() . " " . $exception->getLine() . "\n".
                                $exception->getMessage() . "\n" . $exception->getTraceAsString()
                            );
                            Notification::make()
                                ->title('Error resetting water meter')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation('Are you sure you want to reset the electricity meter for this unit to 0?'),

                // reset-gas-meter
                Tables\Actions\Action::make('reset-gas-meter')
                    ->label('Reset Gas Meter')
                    ->visible(false)
//                    ->visible(function (Unit $record) {
//                        // check if this unit has gas as a utility
//                        return $record?->property?->utilities()
//                            ->whereHas('utility', function (Builder $query) {
//                                $query->where('name', 'Gas');
//                            })
//                            ->exists() && auth()->user()->can(AppPermissions::RESET_GAS_METER_PERMISSION);
//                    })
                    ->action(function (Unit $record) {
                        Notification::make()
                            ->title('Gas meter reset to 0')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation('Are you sure you want to reset the gas meter for this unit to 0?'),

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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UnitsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
