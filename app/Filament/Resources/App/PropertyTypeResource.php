<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\RefPropertyTypeExporter;
use App\Filament\Resources\App\PropertyTypeResource\Pages;
use App\Models\RefPropertyType;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;

class PropertyTypeResource extends Resource
{
    protected static ?string $model = RefPropertyType::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $modelLabel = 'Property Type';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::REFERENCES_NAVIGATION_GROUP;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('type')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable()->limit(50),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(RefPropertyTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()->exporter(RefPropertyTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPropertyTypes::route('/'),
            'create' => Pages\CreatePropertyType::route('/create'),
            'view'   => Pages\ViewPropertyType::route('/{record}'),
            'edit'   => Pages\EditPropertyType::route('/{record}/edit'),
        ];
    }
}
