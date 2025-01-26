<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscalationRatesAndAmountsLogsResource\Pages;
use App\Filament\Resources\EscalationRatesAndAmountsLogsResource\RelationManagers;
use App\Models\EscalationRatesAndAmountsLogs;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EscalationRatesAndAmountsLogsResource extends Resource
{
    protected static ?string $model = EscalationRatesAndAmountsLogs::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tenancy_agreement_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('previous_amount')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_amount')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('escalation_rate')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('escalation_date')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEscalationRatesAndAmountsLogs::route('/'),
            'create' => Pages\CreateEscalationRatesAndAmountsLogs::route('/create'),
            'edit' => Pages\EditEscalationRatesAndAmountsLogs::route('/{record}/edit'),
        ];
    }
}
