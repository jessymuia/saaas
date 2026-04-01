<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\AppRoleExporter;
use App\Filament\Resources\App\AppRoleResource\Pages;
use App\Models\AppRole;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AppRoleResource extends Resource
{
    protected static ?string $model = AppRole::class;
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static UnitEnum|string|null $navigationGroup = 'Access Management';

    // Roles are not distributed with saas_client_id in current schema.
    // If AppRole is scoped per tenant in future, add getEloquentQuery() here.

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('User Permissions')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(255),
                    Forms\Components\CheckboxList::make('permissions')
                        ->label('Permissions')
                        ->columns(4)
                        ->relationship('permissions', 'name')
                        ->options(\Spatie\Permission\Models\Permission::all()->pluck('name', 'id')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->searchable()->sortable(),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(AppRoleExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(AppRoleExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAppRoles::route('/'),
            'create' => Pages\CreateAppRole::route('/create'),
            'edit'   => Pages\EditAppRole::route('/{record}/edit'),
            'view'   => Pages\ViewAppRole::route('/{record}'),
        ];
    }
}
