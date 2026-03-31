<?php

namespace App\Filament\Resources\App;

use App\Filament\Resources\App\EscalationRatesAndAmountsLogsResource\Pages;
use App\Models\EscalationRatesAndAmountsLogs;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EscalationRatesAndAmountsLogsResource extends Resource
{
    protected static ?string $model = EscalationRatesAndAmountsLogs::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tenancy_agreement_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')->searchable(),
                Tables\Columns\TextColumn::make('property.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('previous_amount')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('new_amount')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('escalation_rate')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('escalation_date')->searchable()->sortable(),
            ])
            ->filters([])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEscalationRatesAndAmountsLogs::route('/'),
            'create' => Pages\CreateEscalationRatesAndAmountsLogs::route('/create'),
            'edit'   => Pages\EditEscalationRatesAndAmountsLogs::route('/{record}/edit'),
        ];
    }
}
