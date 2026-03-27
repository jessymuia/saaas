<?php

namespace App\Filament\Resources\App;

use App\Filament\Resources\App\UnitResource\Pages;
use App\Filament\Resources\App\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    /*
    |--------------------------------------------------------------------------
    | TENANT SCOPE — Phase 10.4
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('property_id')
                ->relationship('property', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('unit_type_id')
                ->relationship('unitType', 'type')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('rent_amount')
                ->numeric()
                ->prefix('KES')
                ->required(),
            Forms\Components\Textarea::make('description')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('unitType.type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('rent_amount')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view'   => Pages\ViewUnit::route('/{record}'),
            'edit'   => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
