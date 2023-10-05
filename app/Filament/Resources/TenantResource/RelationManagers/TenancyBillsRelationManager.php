<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Models\MeterReading;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
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
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('generate-bills')
                    ->action(function (): void{
                        // get all meter readings and create
                        MeterReading::query()
                            ->where('has_bill', false)
                            ->select('unit_id', 'utility_id', 'consumption', 'reading_date')
                            ->orderBy('reading_date', 'asc')
                            ->chunk(100, function ($meterReadings) {
                                foreach ($meterReadings as $meterReading) {
                                    $meterReading->createBill();
                                }
                            });
                        Notification::make('generate-bills-notification')
                            ->title('Success')
                            ->send();
                    }),
            ])
            ->actions([

//                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(fn () => 'Are you sure you want to delete this tenancy bill?')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(fn ($records) => 'Are you sure you want to delete these records?'),
                ]),
            ]);
    }
}
