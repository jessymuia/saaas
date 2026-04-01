<?php

namespace App\Filament\Resources\App;

use App\Filament\Resources\App\PropertyManagementUsersResource\Pages;
use App\Models\PropertyManagementUsers;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PropertyManagementUsersResource extends Resource
{
    protected static ?string $model = PropertyManagementUsers::class;
    protected static bool $isScopedToTenant = false;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCESS_MANAGEMENT_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

   
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = filament()->getTenant()?->id;

        return $schema->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name', fn (Builder $query) => $query->where('saas_client_id', $tenantId))
                ->required(),
            Forms\Components\Select::make('property_id')
                ->relationship('property', 'name', fn (Builder $query) => $query->where('saas_client_id', $tenantId))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('property.name')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role.name')->label('Role')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\IconColumn::make('archive')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->defaultSort('property.name')
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPropertyManagementUsers::route('/'),
            'create' => Pages\CreatePropertyManagementUsers::route('/create'),
            'view'   => Pages\ViewPropertyManagementUsers::route('/{record}'),
            'edit'   => Pages\EditPropertyManagementUsers::route('/{record}/edit'),
        ];
    }
}
