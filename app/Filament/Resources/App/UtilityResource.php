<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\RefUtilityExporter;
use App\Filament\Resources\App\UtilityResource\Pages;
use App\Models\RefUtility;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;

class UtilityResource extends Resource
{
    protected static ?string $model = RefUtility::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $modelLabel = 'Utility';

    

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255),
            Forms\Components\TextInput::make('unit_of_measurement')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->label('Additional Information')->maxLength(65535)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('unit_of_measurement')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([\Filament\Actions\ViewAction::make(), \Filament\Actions\EditAction::make()])
            ->headerActions([
                ExportAction::make()->exporter(RefUtilityExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(RefUtilityExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUtilities::route('/'),
            'create' => Pages\CreateUtility::route('/create'),
            'view'   => Pages\ViewUtility::route('/{record}'),
            'edit'   => Pages\EditUtility::route('/{record}/edit'),
        ];
    }
}
